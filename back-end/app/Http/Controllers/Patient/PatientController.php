<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\PatientAppointmentRequest;
use App\Http\Resources\DoctorProfileFullResource;
use App\Services\AppointmentService;
use App\Services\DoctorProfileService;
use OpenApi\Attributes as OA;

class PatientController extends Controller
{
    public function __construct(
        private DoctorProfileService $doctorProfileService,
        private AppointmentService $appointmentService,
    ) {}

    #[OA\Get(
        path: '/api/v1/patient/appointment',
        operationId: 'patientListActiveDoctors',
        summary: 'View approved (active) doctors',
        tags: ['Patient'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list of active doctors', content: new OA\JsonContent(ref: '#/components/schemas/PaginatedDoctorProfileFull')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
        ]
    )]
    public function index()
    {
        return DoctorProfileFullResource::collection($this->doctorProfileService->listActive());
    }

    #[OA\Post(
        path: '/api/v1/patient/appointment',
        operationId: 'patientBookAppointment',
        summary: 'Book an appointment',
        tags: ['Patient'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/PatientAppointmentRequest')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Appointment booked', content: new OA\JsonContent(ref: '#/components/schemas/BookAppointmentResponse')),
            new OA\Response(response: 401, ref: '#/components/responses/Unauthorized'),
            new OA\Response(response: 422, ref: '#/components/responses/ValidationError'),
        ]
    )]
    public function getAppointment(PatientAppointmentRequest $request)
    {
        $appointment = $this->appointmentService->book($request->validated(), $request->user());

        return response()->json([
            'message' => 'نوبت با موفقیت ثبت شد.',
            'data' => $appointment,
        ], 201);
    }
}
