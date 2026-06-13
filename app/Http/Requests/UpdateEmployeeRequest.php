<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('employee'));
    }

    public function rules(): array
    {
        $employee = $this->route('employee');

        return [
            'user_id'                    => ['nullable', 'exists:users,id', Rule::unique('employees', 'user_id')->ignore($employee->id)],
            'department_id'              => 'nullable|exists:departments,id',
            'first_name'                 => 'required|string|max:255',
            'last_name'                  => 'required|string|max:255',
            'id_number'                  => ['required', 'string', Rule::unique('employees')->ignore($employee->id)],
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
            'employee_code'              => ['nullable', 'string', Rule::unique('employees')->ignore($employee->id)],
            // hired_date se omite intencionalmente — no debe ser editable
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
            'delete_documents'           => 'sometimes|array',
            'delete_documents.*'         => 'exists:employee_documents,id',
        ];
    }
}
