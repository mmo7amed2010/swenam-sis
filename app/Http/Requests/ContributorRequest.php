<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Outhebox\TranslationsUI\Enums\RoleEnum;

class ContributorRequest extends FormRequest
{
    public function authorize(): bool
    {
        // All contributor mutations require this ability
        return true;
    }

    public function rules(): array
    {
        $id = (int) ($this->route('id') ?? $this->input('id'));

        return [
            'id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('ltu_contributors', 'email')->ignore($id),
            ],
            'role' => ['required', Rule::in([RoleEnum::owner->value, RoleEnum::translator->value])],
            'password' => $id ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
        ];
    }
}
