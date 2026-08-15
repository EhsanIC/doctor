<?php

namespace App\Http\Controllers\Doctors;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatingAppointmentStatusByDoctorRequest;
use App\Models\Appointment;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DoctorAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: '/api/v1/doctor/appointment',
        operationId: 'doctorListAppointments',
        summary: 'View own appointments',
        tags: ['Doctor'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'List of appointments', content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/Appointment')
            )),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
        ]
    )]
    public function index()
    {
        $doctorProfile = auth()->user()->doctorProfile;

        return Appointment::where('doctor_id', $doctorProfile?->id)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    #[OA\Patch(
        path: '/api/v1/doctor/appointment/{appointment}',
        operationId: 'doctorUpdateAppointmentStatus',
        summary: 'Approve / cancel an appointment',
        description: "Change an appointment's status. Use 'approved' to approve or 'canceled' to cancel.",
        tags: ['Doctor'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\PathParameter(name: 'appointment', description: 'Appointment ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateAppointmentStatusRequest')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated appointment', content: new OA\JsonContent(ref: '#/components/schemas/Appointment')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 403, ref: '#/components/responses/Forbidden'),
            new OA\Response(response: 404, ref: '#/components/responses/NotFound'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
        ]
    )]
    public function update(UpdatingAppointmentStatusByDoctorRequest $request, Appointment $appointment)
    {
        $doctorProfile = auth()->user()->doctorProfile;

        if (! $doctorProfile || $doctorProfile->id !== $appointment->doctor_id) {
            abort(403, 'Unauthorized action.');
        }

        $appointment->update($request->validated());

        return response()->json($appointment->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //
    }
}
