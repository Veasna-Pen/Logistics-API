<?php

namespace App\Services;

use App\Enums\ShipmentStatus;
use App\Jobs\LogShipmentStatus;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShipmentService
{
    public function create(array $data, $userId)
    {
        $shipment = Shipment::create([
            'tracking_code' => strtoupper(Str::random(10)),
            'sender_name' => $data['sender_name'],
            'receiver_name' => $data['receiver_name'],
            'pickup_address' => $data['pickup_address'],
            'delivery_address' => $data['delivery_address'],
            'status' => 'pending'
        ]);

        $shipment->logs()->create([
            'status' => 'pending',
            'updated_by' => $userId
        ]);

        return $shipment;
    }

    public function list()
    {
        return Shipment::with(['driver'])->latest()->paginate(5);
    }

    public function find(int $id): Shipment
    {
        return Shipment::with(['logs', 'driver'])->findOrFail($id);
    }

    public function updateStatus(int $shipmentId, string $newStatus, int $userId)
    {
        return DB::transaction(function () use ($shipmentId, $newStatus, $userId) {
            $shipment = Shipment::findOrFail($shipmentId);

            $currentStatus = $shipment->status;

            if ($currentStatus === $newStatus) {
                return $shipment;
            }

            if (!ShipmentStatus::conTransition($currentStatus, $newStatus)) {
                abort(422, "Invalid transition: $currentStatus → $newStatus");
            }

            $shipment->update([
                'status' => $newStatus
            ]);

            $shipment->logs()->create([
                'status' => $newStatus,
                'updated_by' => $userId
            ]);

            return $shipment;
        });

        LogShipmentStatus::dispatch(
            $shipment->id,
            $currentStatus,
            $newStatus,
            $userId
        );
    }

    public function assignDriver(int $shipmentId, int $driverId, $user)
    {
        return DB::transaction(function () use ($shipmentId, $driverId, $user) {
            $shipment = Shipment::findOrFail($shipmentId);

            if ($shipment->status !== ShipmentStatus::PENDING) {
                abort(422, "Only pending shipments can be assigned");
            }

            $driver = User::findOrFail($driverId);
            if (!$driver->hasRole('driver')) {
                abort(422, "Selected user is not a driver");
            }

            $shipment->update([
                'assigned_driver_id' => $driverId,
                'status' => ShipmentStatus::ASSIGNED
            ]);

            $shipment->logs()->create([
                'status' => ShipmentStatus::ASSIGNED,
                'updated_by' => $user
            ]);

            return $shipment->load(['driver']);
        });
    }
}
