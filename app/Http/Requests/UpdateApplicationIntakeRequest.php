<?php

namespace App\Http\Requests;

use App\Rules\ExistsInLms;
use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationIntakeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'intake_id' => ['required', 'integer', new ExistsInLms('intakes')],
        ];
    }

    public function messages(): array
    {
        return [
            'intake_id.required' => 'Please select an intake.',
            'intake_id.integer' => 'The intake selection is invalid.',
        ];
    }
}
