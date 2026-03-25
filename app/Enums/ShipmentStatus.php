<?php

namespace App\Enums;

class ShipmentStatus
{
    const PENDING = 'pending';
    const ASSIGNED = 'assigned';
    const PICKED_UP = 'picked_up';
    const IN_TRANSIT = 'in_transit';
    const DELIVERED = 'delivered';
    const CANCELLED = 'cancelled';

    public static function all(): array
    {
        return [
            self::PENDING,
            self::ASSIGNED,
            self::PICKED_UP,
            self::IN_TRANSIT,
            self::DELIVERED,
            self::CANCELLED,
        ];
    }

    // Define allowed transitions
    public static function transitions(): array
    {
        return [
            self::PENDING => [self::ASSIGNED, self::CANCELLED],
            self::ASSIGNED => [self::PICKED_UP, self::CANCELLED],
            self::PICKED_UP => [self::IN_TRANSIT],
            self::IN_TRANSIT => [self::DELIVERED],
            self::DELIVERED => [],
            self::CANCELLED => [],
        ];
    }

    public static function conTransition(string $from, string $to): bool
    {
        return in_array($to, self::transitions()[$from] ?? []);
    }
}
