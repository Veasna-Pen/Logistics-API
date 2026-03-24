<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->string('sender_name');
            $table->string('receiver_name');
            $table->string('pickup_address');
            $table->string('delivery_address');
            $table->string('status')->index();
            $table->foreignId('assigned_driver_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
