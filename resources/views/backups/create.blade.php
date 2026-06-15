@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Encabezado --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-pc-blue dark:text-white tracking-tight flex items-center gap-3">
                    <i class="fas fa-cloud-upload-alt text-pc-orange"></i> Subir Respaldo Externo
                </h1>
                <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mt-1">
                    Carga un archivo de respaldo (SQL, ZIP, GZ) para restaurarlo
                </p>
            </div>
            <a href="{{ route('backups.index') }}" class="btn-pc-secondary text-xs px-4 py-2 flex items-center shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver al listado
            </a>
        </div>

        <div class="card-pc border-t-4 border-pc-orange overflow-hidden relative dark:bg-slate-900 dark:border-slate-800 p-6">
            <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                <i class="fas fa-database text-9xl text-pc-orange"></i>
            </div>

            <form action="{{ route('backups.upload') }}" method="POST" enctype="multipart/form-data" class="relative z-10" id="upload-backup-form">
                @csrf
                
                <div class="mb-6">
                    <label class="label-pc mb-2">Archivo de Respaldo <span class="text-pc-red">*</span></label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-file-archive text-gray-400 group-focus-within:text-pc-orange transition-colors"></i>
                        </div>
                        <input type="file" name="backup_file" accept=".sql,.zip,.gz" required
                               class="block w-full pl-10 text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-pc-orange/10 file:text-pc-orange hover:file:bg-pc-orange/20 dark:bg-slate-800 dark:border-slate-700 dark:text-white border border-gray-200 rounded-xl cursor-pointer focus:outline-none transition-all">
                    </div>
                    @error('backup_file') 
                        <p class="mt-2 text-xs text-red-600 font-bold flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p> 
                    @enderror
                </div>

                <div class="bg-blue-50/50 dark:bg-slate-800/50 p-4 rounded-xl border border-blue-100 dark:border-slate-700 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-pc-blue mt-0.5"></i>
                        <div class="text-xs text-gray-600 dark:text-gray-300">
                            <p class="font-bold mb-1">Notas importantes:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>El archivo no se restaurará automáticamente, solo se guardará en el servidor.</li>
                                <li>Podrás restaurarlo posteriormente desde el panel principal de respaldos.</li>
                                <li>Asegúrate de que el archivo no contenga errores de sintaxis y corresponda a la estructura de la base de datos actual.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-slate-800">
                    <a href="{{ route('backups.index') }}" class="btn-pc-secondary text-xs px-5 py-2.5">Cancelar</a>
                    <button type="submit" class="bg-pc-orange hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-lg shadow-orange-100 dark:shadow-none transition-all transform hover:scale-[1.02] flex items-center gap-2" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Subiendo...'; this.classList.add('opacity-75', 'cursor-not-allowed'); this.form.submit();">
                        <i class="fas fa-upload"></i> Subir Archivo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection