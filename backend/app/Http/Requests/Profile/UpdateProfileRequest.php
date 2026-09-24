<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'          => ['sometimes', 'nullable', 'string', 'max:100'],
            'first_name'    => ['sometimes', 'nullable', 'string', 'max:50'],
            'last_name'     => ['sometimes', 'nullable', 'string', 'max:50'],
            'email'         => ['sometimes', 'nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($this->user()?->id ?? auth()->id())],
            'phone'         => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($this->user()?->id ?? auth()->id())],
            'avatar'        => ['nullable'],
            'avatar_url'    => ['nullable'],
            'bio'           => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender'        => ['nullable', 'in:male,female,other'],
            'address'       => ['nullable', 'string'],
            'city_id'       => ['nullable', 'exists:cities,id'],
            'preferred_contact_method' => ['nullable', 'string'],
        ];
    }
}
