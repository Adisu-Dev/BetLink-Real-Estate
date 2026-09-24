<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'property_id'  => ['required', 'exists:properties,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'type'         => ['nullable', 'in:in_person,virtual'],
            'message'      => ['nullable', 'string', 'max:1000'],
        ];
    }
}
