<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\PatientAppointmentRequest;
use App\Models\Appointment;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index () {
        return DoctorProfile::whereStatus('active')->get();
    }

    public function getAppointment (PatientAppointmentRequest $request) {
        $appointment = Appointment::create([
            'user_id' => auth()->id(),
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'نوبت با موفقیت ثبت شد.',
            'data' => $appointment
        ], 201);
    }
}
