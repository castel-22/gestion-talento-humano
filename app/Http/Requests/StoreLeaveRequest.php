<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Leave::class);
    }

    public function rules(): array
    {
        return [
            'employee_id'         => 'required|exists:employees,id',
            'start_date'          => 'required|date',
            'duration_value'      => 'required|integer|min:1',
            'duration_unit'       => 'required|in:days,weeks,months',
            'doctor_name'         => 'required|string|max:255',
            'issuing_institution' => 'required|string|max:255',
            'medical_condition'   => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required'         => 'El empleado es obligatorio.',
            'start_date.required'          => 'La fecha de inicio es obligatoria.',
            'duration_value.required'      => 'La duración es obligatoria.',
            'duration_unit.required'       => 'La unidad de duración es obligatoria.',
            'doctor_name.required'         => 'El nombre del médico es obligatorio.',
            'issuing_institution.required' => 'La institución emisora es obligatoria.',
        ];
    }
}
