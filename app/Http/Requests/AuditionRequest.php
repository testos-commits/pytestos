<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuditionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'country_id' => 'required|exists:countries,id',
            'title' => 'required|string',
            'title_ar' => 'nullable|string',
            'description' => 'required',
            'description_ar' => 'nullable',
            'image' => 'required',
            'image_ar' => 'nullable',
            'location' => 'nullable',
            'location_ar' => 'nullable',
            'filming_date' => 'nullable|date',
            'applications_deadline' => 'nullable|date',
            'filming_budget' => 'nullable|numeric',
            'usage_budget' => 'nullable|numeric',
            'currency' => 'required_with:filming_budget,usage_budget|string',
            'show_budget' => 'boolean',
            'duration' => 'nullable',
            'duration_ar' => 'nullable',
            'meta' => 'nullable',
        ];
    }
}
