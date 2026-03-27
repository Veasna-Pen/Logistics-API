<?php

namespace App\Http\Controllers\Api;

use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShipmentController extends Controller
{
    protected $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'sender_name' => 'required|string|max:255',
            'receiver_name' => 'required|string|max:255',
            'pickup_address' => 'required|string',
            'delivery_address' => 'required|string',
        ]);

        $shipment =  $this->shipmentService->create(
            $request->all(),
            auth()->id()
        );

        return response()->json($shipment, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', ShipmentStatus::all())
        ]);

        $shipment = $this->shipmentService->updateStatus($id, $request->status, auth()->id());

        return response()->json($shipment);
    }
    
    public function index()
    {
        return response()->json(
            $this->shipmentService->list()
        );
    }

    public function show($id)
    {
        return response()->json(
            $this->shipmentService->find($id)
        );;
    }
}
