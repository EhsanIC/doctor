<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'doctor_id' => $this->doctor_id,
            'appointment_date' => $this->appointment_date,
            'appointment_time' => $this->appointment_time,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'doctor_profile' => $this->whenLoaded('doctorProfile', function () {
                return [
                    'id' => $this->doctorProfile->id,
                    'name' => $this->doctorProfile->user?->name,
                    'specialty' => $this->doctorProfile->specialty ? [
                        'id' => $this->doctorProfile->specialty->id,
                        'name' => $this->doctorProfile->specialty->name,
                    ] : null,
                ];
            }),
        ];
    }
}
