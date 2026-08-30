<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientDoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user?->name,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],
            'specialty' => $this->specialty ? [
                'id' => $this->specialty->id,
                'name' => $this->specialty->name,
            ] : null,
            'specialty_name' => $this->specialty?->name,
            'image_url' => $this->image
                ? route('patient.doctor.profile.image', ['profile' => $this->id])
                : null,
            'bio' => $this->bio,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'working_hours' => $this->working_hours,
        ];
    }
}
