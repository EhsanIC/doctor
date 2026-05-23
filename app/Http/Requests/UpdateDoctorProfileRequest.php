<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'specialty_id' => 'sometimes|required|integer|exists:specialties,id',
            'status' => 'sometimes|required|in:active,inactive,pending', // مثال: فقط مقادیر مجاز
            'image' => 'sometimes|required|string|max:255', // مسیر تصویر یا URL
            'bio' => 'sometimes|required|string',
            'mobile' => 'sometimes|required|string|max:20',
            'medical_code' => 'sometimes|required|string|max:50',
            'address' => 'sometimes|required|string',
            'working_hours' => 'sometimes|required|string',
        ];
    }
}
