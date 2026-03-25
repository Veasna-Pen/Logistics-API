<?php

namespace App\Services;

use App\Models\Shipment;
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
}
