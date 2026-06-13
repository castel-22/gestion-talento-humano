@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-6">Editar Datos Personales: {{ $employee->full_name }}</h2>
                <form method="POST" action="{{ route('employees.update.personal', $employee) }}">
                    @csrf
                    @method('PUT')
                    @include('employees._form_personal', ['employee' => $employee])
                    <div class="flex justify-end space-x-2 mt-4">
                        <a href="{{ route('employees.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded">Cancelar</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">Actualizar Personales</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection