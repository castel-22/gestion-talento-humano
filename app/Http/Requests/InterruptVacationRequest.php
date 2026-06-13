<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InterruptVacationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('vacation'));
    }

    public function rules(): array
    {
        return [
            'interruption_reason' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'interruption_reason.required' => 'El motivo de la interrupción es obligatorio.',
        ];
    }
}
