<?php

namespace App\Enums;

/**
 * Only used for expenses in the "maintenance" category: is the problem fixed yet?
 * (Replaces the five statuses of the old Maintenance screen.)
 */
enum RepairStatus: string
{
    case Open = 'open';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Still to fix',
            self::Done => 'Fixed',
        };
    }
}
