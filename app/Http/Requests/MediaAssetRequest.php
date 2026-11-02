<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
class MediaAssetRequest extends FormRequest
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
            'url' => 'string|required',
            'type' => [new Enum(MediaAssetType::class), 'required'],
            'casting_image_type_id' => 'integer|required_if:type,casting_image',
            'video_thumbnail' => 'string|required_if:type,video',
        ];
    }
}

enum MediaAssetType: string {
    case VIDEO = 'video';
    case PROFILE_IMAGE = 'profile_image';
    case CASTING_IMAGE = 'casting_image';
}