<?php

namespace App\Http\Controllers\Doctors;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatingAppointmentStatusByDoctorRequest;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DoctorAppointmentController extends Controller
{
    public function __construct(private AppointmentService $service) {}

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
        return $this->service->listForDoctor(auth()->user()->doctorProfile);
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
        $this->authorize('updateStatus', $appointment);

        $appointment = $this->service->updateStatus($appointment, $request->validated());

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
