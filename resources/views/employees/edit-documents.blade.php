@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-6">Editar Documentos: {{ $employee->full_name }}</h2>
                <form method="POST" action="{{ route('employees.update.documents', $employee) }}" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    
                    @if($employee->documents->count() > 0)
                        <div class="mb-4">
                            <h4 class="font-semibold mb-2">Documentos actuales:</h4>
                            @foreach($employee->documents as $doc)
                                <div class="flex items-center justify-between border p-2 mb-2">
                                    <div>
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-blue-600 hover:underline">
                                            {{ $doc->file_name }}
                                        </a>
                                        <span class="text-sm text-gray-600 ml-2">({{ $doc->title }})</span>
                                    </div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="delete_documents[]" value="{{ $doc->id }}" class="mr-1">
                                        <span class="text-sm text-red-600">Eliminar</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @include('employees._form_documents')

                    <div class="flex justify-end space-x-2 mt-4">
                        <a href="{{ route('employees.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded">Cancelar</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">Actualizar Documentos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection