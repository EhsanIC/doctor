<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorProfileFullResource extends JsonResource
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
            'specialty_id' => $this->specialty_id,
            'status' => $this->status,
            'image' => $this->image ? route('patient.doctor.profile.image', ['profile' => $this->id]) : null,
            'image_url' => $this->image ? route('patient.doctor.profile.image', ['profile' => $this->id]) : null,
            'bio' => $this->bio,
            'mobile' => $this->mobile,
            'medical_code' => $this->medical_code,
            'address' => $this->address,
            'working_hours' => $this->working_hours,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'specialty' => new SpecialtyResource($this->whenLoaded('specialty')),
        ];
    }
}
