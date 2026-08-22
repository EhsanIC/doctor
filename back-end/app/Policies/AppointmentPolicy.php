<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Determine whether the doctor owns the appointment.
     */
    public function updateStatus(User $user, Appointment $appointment): bool
    {
        return $user->doctorProfile?->id === $appointment->doctor_id;
    }
}
