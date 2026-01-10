<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            // Name
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],

            // Contact Info
            'phone' => ['nullable', 'string', 'max:50'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state_province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],

            // Emergency Contact
            'emergency_first_name' => ['nullable', 'string', 'max:100'],
            'emergency_last_name' => ['nullable', 'string', 'max:100'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],
            'emergency_address_line1' => ['nullable', 'string', 'max:255'],
            'emergency_address_line2' => ['nullable', 'string', 'max:255'],
            'emergency_city' => ['nullable', 'string', 'max:100'],
            'emergency_state_province' => ['nullable', 'string', 'max:100'],
            'emergency_postal_code' => ['nullable', 'string', 'max:20'],
            'emergency_country' => ['nullable', 'string', 'max:100'],

            // Profile Photo
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'profile_photo_remove' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'profile_photo.image' => 'The file must be an image.',
            'profile_photo.mimes' => 'Allowed image types: JPEG, PNG, JPG, GIF.',
            'profile_photo.max' => 'Image must be less than 2MB.',
        ];
    }
}
