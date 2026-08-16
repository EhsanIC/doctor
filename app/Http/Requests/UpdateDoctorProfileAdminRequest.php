<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorProfileAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // add midd admin
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'specialty_id' => 'sometimes|exists:specialties,id',
            'status' => 'sometimes|in:pending,active,disabled',
            'image' => 'sometimes|string|max:255',
            'bio' => 'sometimes|string|max:2000',
        ];
    }
}
