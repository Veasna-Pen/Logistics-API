<?php

namespace App\Services;

use App\Enums\ShipmentStatus;
use App\Models\Shipment;
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

            if (!ShipmentStatus::conTransition($currentStatus, $newStatus)) {
                throw new \Exception("Invalid status transition: $currentStatus → $newStatus");
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
    }
}
