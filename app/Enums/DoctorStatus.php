<?php

namespace App\Enums;

enum DoctorStatus: string
{
    case PENDING = 'pending';       // Awaiting admin approval
    case ACTIVE = 'active';         // Approved and working
    case DISABLED = 'disabled';     // Disabled by admin
}
