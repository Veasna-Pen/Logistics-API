<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tracking_code',
        'sender_name',
        'receiver_name',
        'pickup_address',
        'delivery_address',
        'status',
        'assign_driver_id'
    ];

    // One shipments has many logs
    public function logs(){
        return $this->hasMany(ShipmentStatusLog::class);
    }

    //Relation to assigned driver
    public function driver(){
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }
}
