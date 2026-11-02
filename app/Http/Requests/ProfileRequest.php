<?php

namespace App\Http\Requests;

use App\Rules\isAttribute;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Attribute;

class ProfileRequest extends FormRequest
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
        $attributesRules = [];
        $attributes = Attribute::all()->pluck('slug')->toArray();

        foreach ($attributes as $attribute) {
            $attributesRules[$attribute] = ['sometimes', new isAttribute];
        }


        return [
            'first_name' => 'sometimes|required|string',
            'last_name' => 'sometimes|required|string',
            'description' => 'sometimes|string|nullable',
            // 'country_id' => 'sometimes|exists:countries,id',
            'date_of_birth' => 'nullable|date',
            'city' => 'nullable',
            'street' => 'nullable',
            'building' => 'nullable',
            'phone' => 'required_with:country_code_country_id',
            "country_code_country_id" => 'required_with:phone|exists:countries,id',
            'gender' => 'sometimes',
            'major' => 'nullable',
            'university' => 'nullable',
            'occupation' => 'nullable',
            'height' => 'sometimes',
            'weight' => 'sometimes',
            'shoes' => 'sometimes',
            'pants' => 'sometimes',
            'shirt' => 'sometimes',
            'video' => 'sometimes',
            'previous_commercials' => 'sometimes|array',
            'previous_commercials.*' => 'sometimes|string',
            'nationality_ids' => 'sometimes|array',
            'nationality_ids.*' => 'sometimes|exists:countries,id',
            'passport_ids' => 'sometimes|array',
            'passport_ids.*' => 'sometimes|exists:countries,id',
            'social_media_accounts' => 'sometimes|array',
            'social_media_accounts.*' => 'nullable',
            'wallet_updated_on' => 'sometimes',
            ...$attributesRules
        ];
    }
}
