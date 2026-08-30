<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class AppointmentService
{
    /**
     * List appointments belonging to a doctor, paginated and eager loaded.
     */
    public function listForDoctor(DoctorProfile $profile): LengthAwarePaginator
    {
        return Appointment::with(['user', 'doctorProfile'])
            ->where('doctor_id', $profile->id)
            ->paginate();
    }

    /**
     * List appointments belonging to a patient, paginated and eager loaded.
     */
    public function listForPatient(User $user): LengthAwarePaginator
    {
        return Appointment::with(['doctorProfile.user', 'doctorProfile.specialty'])
            ->where('user_id', $user->id)
            ->latest('appointment_date')
            ->latest('appointment_time')
            ->paginate();
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
