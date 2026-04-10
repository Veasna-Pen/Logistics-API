<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class LogShipmentStatus implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $shipmentId,
        public string $from,
        public string $to,
        public int $userId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Shipment status updated (async)", [
            'shipment_id' => $this->shipmentId,
            'from' => $this->from,
            'to' => $this->to,
            'updated_by' => $this->userId,
        ]);
    }
}
