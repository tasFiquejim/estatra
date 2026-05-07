<?php

namespace App\Enums;

enum LeaseStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Terminated = 'terminated';
}
