<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case Pending    = 'pending';
    case InProgress = 'in_progress';
    case Completed  = 'completed';
    case OnHold     = 'on_hold';
    case Scheduled  = 'scheduled';

    public function label(): string
    {
        return match($this) {
            self::Pending    => 'Pending',
            self::InProgress => 'In Progress',
            self::Completed  => 'Completed',
            self::OnHold     => 'On Hold',
            self::Scheduled  => 'Scheduled',
        };
    }
}
