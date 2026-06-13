<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVacationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Vacation::class);
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'start_date'  => 'required|date|after_or_equal:today',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'regular_days_to_take' => 'required|integer|min:0',
            'accumulated_days_to_take' => 'required|integer|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $reg = (int) $this->input('regular_days_to_take', 0);
            $acc = (int) $this->input('accumulated_days_to_take', 0);
            if ($reg + $acc <= 0) {
                $validator->errors()->add('days_total', 'Debe solicitar al menos 1 día de vacaciones en total.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Debe seleccionar un empleado.',
            'start_date.required'  => 'La fecha de inicio es obligatoria.',
            'start_date.after_or_equal' => 'La fecha de inicio no puede ser en el pasado.',
            'end_date.required'    => 'La fecha de fin es obligatoria.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la de inicio.',
            'regular_days_to_take.required'  => 'Los días regulares son obligatorios.',
            'accumulated_days_to_take.required' => 'Los días acumulados son obligatorios.',
        ];
    }
}
