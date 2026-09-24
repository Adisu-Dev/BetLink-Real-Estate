<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        if ($this->has('role')) {
            $normalizedRole = match(strtolower(trim((string)$this->role))) {
                'buyer', 'buyer / tenant', 'buyer_tenant', 'buyer / renter', 'buyer_renter' => 'buyer',
                'owner', 'property owner', 'property_owner' => 'owner',
                'agent', 'real estate agent', 'real_estate_agent' => 'agent',
                default => 'buyer'
            };
            $this->merge(['role' => $normalizedRole]);
        }
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['nullable', 'string', 'min:6'],
            'role'     => ['nullable', 'string'],
        ];
    }
}

