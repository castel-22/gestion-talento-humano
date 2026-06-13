@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-4">Cargar Respaldo Externo</h2>
                <p class="mb-4 text-gray-600">Selecciona un archivo de respaldo (SQL, ZIP o GZ) para subirlo al sistema. Luego podrás restaurarlo.</p>

                <form action="{{ route('backups.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Archivo de respaldo</label>
                        <input type="file" name="backup_file" accept=".sql,.zip,.gz" class="border rounded w-full py-2 px-3" required>
                        @error('backup_file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('backups.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded">Cancelar</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">Subir Archivo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection