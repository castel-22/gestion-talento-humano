@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-4">Editar Departamento</h2>

                <form method="POST" action="{{ route('departments.update', $department) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $department->name) }}" 
                               class="border rounded w-full py-2 px-3 focus:outline-none focus:ring focus:border-blue-300" required>
                        @error('name')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Descripción</label>
                        <textarea name="description" id="description" rows="4" 
                                  class="border rounded w-full py-2 px-3 focus:outline-none focus:ring focus:border-blue-300">{{ old('description', $department->description) }}</textarea>
                        @error('description')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="logo" class="block text-gray-700 text-sm font-bold mb-2">Logo del departamento</label>
                        @if($department->logo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/'.$department->logo) }}" alt="Logo" class="w-32 h-32 object-cover">
                            </div>
                        @endif
                        <input type="file" name="logo" id="logo" accept="image/*" class="border rounded w-full py-2 px-3">
                        <p class="text-sm text-gray-500">Dejar vacío para mantener el logo actual.</p>
                        @error('logo')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('departments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded">Cancelar</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection