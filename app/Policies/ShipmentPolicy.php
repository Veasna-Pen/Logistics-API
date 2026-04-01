<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    public function view(User $user, Shipment $shipment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('driver')) {
            return $shipment->assigned_driver_id === $user->id;
        }

        return false;
    }

    public function update(User $user, Shipment $shipment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($user->hasRole('driver')) {
            return $shipment->assigned_driver_id === $user->id;
        }
        return false;
    }

    public function assign(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
