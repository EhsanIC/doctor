<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AppointmentService
{
    /**
     * List appointments belonging to a doctor.
     */
    public function listForDoctor(DoctorProfile $profile): Collection
    {
        return Appointment::where('doctor_id', $profile->id)->get();
    }

    /**
     * Update an appointment's status (ownership is enforced by a Policy).
     */
    public function updateStatus(Appointment $appointment, array $data): Appointment
    {
        $appointment->update($data);

        return $appointment;
    }

    /**
     * Book a new appointment for a patient with a pending status.
     */
    public function book(array $data, User $user): Appointment
    {
        return Appointment::create([
            'user_id' => $user->id,
            'doctor_id' => $data['doctor_id'],
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'description' => $data['description'] ?? null,
            'status' => 'pending',
        ]);
    }
}
