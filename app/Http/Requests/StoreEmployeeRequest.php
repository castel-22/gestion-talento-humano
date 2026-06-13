<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Employee::class);
    }

    public function rules(): array
    {
        return [
            'user_id'                    => 'nullable|exists:users,id|unique:employees,user_id',
            'department_id'              => 'nullable|exists:departments,id',
            'first_name'                 => 'required|string|max:255',
            'last_name'                  => 'required|string|max:255',
            'id_number'                  => 'required|string|unique:employees,id_number',
            'birth_date'                 => 'nullable|date',
            'birth_place'                => 'nullable|string|max:255',
            'marital_status'             => 'nullable|in:soltero,casado,divorciado,viudo,otro',
            'address'                    => 'nullable|string',
            'sector'                     => 'nullable|string|max:255',
            'parish'                     => 'nullable|string|max:255',
            'personal_phone'             => 'nullable|string|max:20',
            'home_phone'                 => 'nullable|string|max:20',
            'email'                      => 'nullable|email|max:255',
            'blood_type'                 => 'nullable|string|max:10',
            'allergies'                  => 'nullable|string',
            'emergency_contact_name'     => 'nullable|string|max:255',
            'emergency_contact_phone'    => 'nullable|string|max:20',
            'education_level'            => 'nullable|string|max:255',
            'degree'                     => 'nullable|string|max:255',
            'institution'                => 'nullable|string|max:255',
            'graduation_year'            => 'nullable|integer|min:1900|max:' . date('Y'),
            'currently_studying'         => 'sometimes|boolean',
            'specializations'            => 'nullable|string',
            'employee_code'              => 'nullable|string|unique:employees,employee_code',
            'hired_date'                 => 'required|date',
            'position'                   => 'required|string|max:255',
            'employment_type'            => 'required|in:fijo,contratado,comision',
            'status'                     => 'required|in:activo,inactivo,reposo',
            'employee_type'              => 'nullable|in:gobernacion,alcaldia,homologado,nacional',
            'gender'                     => 'nullable|in:masculino,femenino,otro',
            'position_id'                => 'nullable|exists:positions,id',
            'rank_id'                    => 'nullable|exists:ranks,id',
            'institutional_code'         => 'nullable|string|max:255',
            'new_documents'              => 'sometimes|array',
            'new_documents.*.title'      => 'required_with:new_documents|string',
            'new_documents.*.file'       => 'required_with:new_documents|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'new_documents.*.type'       => 'required_with:new_documents|string',
            'new_documents.*.description'=> 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'    => 'El nombre es obligatorio.',
            'last_name.required'     => 'El apellido es obligatorio.',
            'id_number.required'     => 'La cédula es obligatoria.',
            'id_number.unique'       => 'Ya existe un empleado con esta cédula.',
            'hired_date.required'    => 'La fecha de ingreso es obligatoria.',
            'position.required'      => 'El cargo es obligatorio.',
            'employment_type.required'=> 'El tipo de empleado es obligatorio.',
            'status.required'        => 'El estado es obligatorio.',
        ];
    }
}
