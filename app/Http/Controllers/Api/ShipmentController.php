<?php

namespace App\Http\Controllers\Api;

use App\Enums\ShipmentStatus;
use App\Helpers\ApiResponse;
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

    public function index()
    {
        return ApiResponse::success($this->shipmentService->list());
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
            auth()->user()->id
        );

        return ApiResponse::success($shipment, 'Shipment created successfully', 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', ShipmentStatus::all())
        ]);

        $shipment = Shipment::findOrFail($id);
        $this->authorize('update', $shipment);
        $result = $this->shipmentService->updateStatus($id, $request->status, auth()->user()->id);

        return ApiResponse::success($result, 'Shipment status updated successfully');
    }

    public function show($id)
    {

        $shipment = $this->shipmentService->find($id);
        $this->authorize('view', $shipment);

        return ApiResponse::success($shipment);
    }

    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id'
        ]);

        $this->authorize('assign', Shipment::class);

        $shipment = $this->shipmentService->assignDriver($id, $request->driver_id, auth()->user()->id);

        return ApiResponse::success($shipment, 'Driver assigned successfully');
    }
}
