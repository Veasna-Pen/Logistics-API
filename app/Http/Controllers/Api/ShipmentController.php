<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShipmentController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'sender_name' => 'required|string|max:255',
            'receiver_name' => 'required|string|max:255',
            'pickup_address' => 'required|string',
            'delivery_address' => 'required|string',
        ]);

        $shipment = Shipment::create([
            'tracking_code' => strtoupper(Str::random(10)),
            'sender_name' => $request->sender_name,
            'receiver_name' => $request->receiver_name,
            'pickup_address' => $request->pickup_address,
            'delivery_address' => $request->delivery_address,
            'status' => 'pending',
        ]);

        return response()->json([
            $shipment
        ], 201);
    }

    public function index()
    {
        $shipment = Shipment::latest()->paginate(5);
        return response()->json($shipment);
    }

    public function show($id)
    {
        $shipment = Shipment::with(['logs', 'driver'])->findOrFail($id);
        return response()->json($shipment);
    }
}
