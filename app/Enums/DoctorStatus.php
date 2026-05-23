<?php

namespace App\Enums;

enum DoctorStatus: string
{
    case PENDING = 'pending';       // Awaiting Verification
    case ACTIVE = 'active';         // Fully operational
    case SUSPENDED = 'suspended';   // Temporarily inactive (by Admin)
    case VACATION = 'vacation';     // Temporarily inactive (by Doctor)
    case ARCHIVED = 'archived';     // Left the platform
}
