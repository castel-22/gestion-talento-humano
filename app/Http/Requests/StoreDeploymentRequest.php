<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeploymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'place'             => 'required|string|max:255',
            'reason'            => 'required|string',
            'division'          => 'nullable|string|max:255',
            'supervisor_id'     => 'required|exists:employees,id',
            'start_datetime'    => 'required|date',
            'end_datetime'      => 'nullable|date|after_or_equal:start_datetime',
            'is_indefinite'     => 'sometimes|boolean',
            'notes'             => 'nullable|string',
            'latitude'          => 'nullable|numeric',
            'longitude'         => 'nullable|numeric',
            'participants'      => 'required|array|min:1',
            // Si participants es un array de IDs (como en la búsqueda simple)
            // o un array asociativo (como en la edición avanzada)
            // La validación debe ser flexible o el controlador debe normalizarlo.
        ];
    }

    public function messages(): array
    {
        return [
            'place.required'          => 'La ubicación/lugar es obligatoria.',
            'reason.required'         => 'El motivo del despliegue es obligatorio.',
            'supervisor_id.required'  => 'El supervisor es obligatorio.',
            'start_datetime.required' => 'La fecha y hora de inicio son obligatorias.',
            'participants.required'   => 'Debe seleccionar al menos un participante.',
            'participants.min'        => 'Debe seleccionar al menos un participante.',
        ];
    }
}
