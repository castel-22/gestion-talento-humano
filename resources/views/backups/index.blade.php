@extends('layouts.app')

@section('breadcrumbs')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 hover:text-pc-orange inline-flex items-center">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
                <span class="text-sm text-pc-orange font-medium">Respaldos del Sistema</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- ══ Cabecera ══ --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-pc-blue uppercase tracking-tight flex items-center gap-4">
                <i class="fas fa-server text-pc-orange"></i> Bóveda de Datos
            </h2>
            <p class="text-gray-400 text-[11px] font-bold uppercase tracking-[0.2em] mt-2">Seguridad y Resiliencia Estructural</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('backups.create') }}" class="btn-pc-secondary">
                <i class="fas fa-upload text-[10px]"></i> Cargar Externo
            </a>
            <form id="form-generate-backup" action="{{ route('backups.store') }}" method="POST" class="inline no-double-click">
                @csrf
                <button type="submit" class="btn-pc-primary">
                    <i class="fas fa-database text-[10px]"></i> Generar Respaldo
                </button>
            </form>
        </div>
    </div>

    {{-- ══ Métricas en Tiempo Real ══ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
        <div class="card-pc p-5 flex items-center gap-4 border-l-4 border-l-pc-blue">
            <div class="w-11 h-11 rounded-xl bg-pc-blue/5 text-pc-blue flex items-center justify-center text-xl"><i class="fas fa-hard-drive"></i></div>
            <div>
                <span class="block text-2xl font-black text-pc-blue">{{ $backups->total() ?? $backups->count() }}</span>
                <span class="text-[8px] text-gray-400 uppercase font-black tracking-widest">Total de Copias</span>
            </div>
        </div>
        <div class="card-pc p-5 flex items-center gap-4 border-l-4 border-l-green-500">
            <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl"><i class="fas fa-clock-rotate-left"></i></div>
            <div>
                <span class="block text-2xl font-black text-green-600">{{ $backups->first() ? $backups->first()->created_at->diffForHumans() : 'N/A' }}</span>
                <span class="text-[8px] text-gray-400 uppercase font-black tracking-widest">Último Respaldo</span>
            </div>
        </div>
        <div class="card-pc p-5 flex items-center gap-4 border-l-4 border-l-pc-orange">
            <div class="w-11 h-11 rounded-xl bg-pc-orange/5 text-pc-orange flex items-center justify-center text-xl"><i class="fas fa-microchip"></i></div>
            <div>
                <span class="block text-xl font-black text-pc-orange mt-1">ESTABLE</span>
                <span class="text-[8px] text-gray-400 uppercase font-black tracking-widest">Estado del Motor</span>
            </div>
        </div>
    </div>

    {{-- ══ Historial de Respaldos ══ --}}
    <div class="card-pc border-t-4 border-pc-blue">
        <div class="bg-gray-50/50 dark:bg-slate-900/50 p-6 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center">
            <h3 class="text-sm font-black text-pc-blue dark:text-gray-200 uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-list-check opacity-50 dark:opacity-70 dark:text-pc-orange"></i> Registro Histórico
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800/80 border-b-2 border-gray-200 dark:border-slate-700">
                        <th class="px-6 py-4 text-[9px] font-black text-gray-400 dark:text-gray-300 uppercase tracking-widest">Archivo</th>
                        <th class="px-6 py-4 text-[9px] font-black text-gray-400 dark:text-gray-300 uppercase tracking-widest">Creación</th>
                        <th class="px-6 py-4 text-[9px] font-black text-gray-400 dark:text-gray-300 uppercase tracking-widest">Tamaño</th>
                        <th class="px-6 py-4 text-[9px] font-black text-gray-400 dark:text-gray-300 uppercase tracking-widest">Operador</th>
                        <th class="px-6 py-4 text-[9px] font-black text-gray-400 dark:text-gray-300 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800/50 bg-white dark:bg-slate-900">
                    @forelse($backups as $backup)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-file-zipper text-pc-orange opacity-50 dark:opacity-80"></i>
                                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ $backup->filename }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $backup->created_at->format('d/m/Y') }}</span>
                                <span class="text-[9px] text-gray-400 font-bold">{{ $backup->created_at->format('H:i') }} hrs</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-gray-600 dark:text-gray-400">
                            {{ number_format($backup->size, 2) }} KB
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-gray-600 dark:text-gray-400">
                            <i class="fas fa-user-shield text-[10px] mr-1 text-gray-400 dark:text-gray-500"></i>
                            {{ $backup->creator->name ?? 'Sistema Automático' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-100 lg:opacity-60 lg:group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('backups.download', $backup) }}" title="Descargar Copia"
                                   class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-600 dark:hover:bg-blue-500 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-download text-[10px]"></i>
                                </a>
                                
                                <form action="{{ route('backups.restore', $backup) }}" method="POST" class="inline no-double-click form-restore-backup" data-date="{{ $backup->created_at->format('d/m/Y H:i') }}">
                                    @csrf
                                    <button type="submit" title="Restaurar Sistema" 
                                            class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-clock-rotate-left text-[10px]"></i>
                                    </button>
                                </form>
                                
                                <form action="{{ route('backups.destroy', $backup) }}" method="POST" class="inline no-double-click form-delete-backup confirm-delete" data-label="este respaldo">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Eliminar Respaldo"
                                            class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-trash text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-bold text-xs uppercase tracking-widest">
                            <i class="fas fa-database text-4xl mb-3 opacity-20 block"></i>
                            No hay respaldos almacenados en el sistema
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-100 dark:border-slate-800">
            {{ $backups->links() }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Generar Respaldo
    const formGenerate = document.getElementById('form-generate-backup');
    if (formGenerate) {
        formGenerate.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Generando Respaldo',
                text: 'Compilando y exportando la base de datos de Talento Humano. Por favor, espere...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            this.submit();
        });
    }

    // 2. Restaurar Sistema
    document.querySelectorAll('.form-restore-backup').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const dateStr = this.dataset.date;
            Swal.fire({
                title: '⚠️ ADVERTENCIA CRÍTICA',
                html: `¿Estás completamente seguro de querer restaurar la base de datos?<br><br><b>TODOS los registros creados DESPUÉS del ${dateStr} se perderán de forma IRREVERSIBLE.</b><br><br>Para proceder, escribe <b>ACEPTAR</b> a continuación:`,
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Escribe ACEPTAR',
                showCancelButton: true,
                confirmButtonText: 'Restaurar base de datos',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#e0a96d', // pc-orange
                cancelButtonColor: '#1e293b', // slate-800
                inputValidator: (value) => {
                    if (value !== 'ACEPTAR') {
                        return 'Debes escribir ACEPTAR para continuar.';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Restaurando Base de Datos',
                        text: 'Re-escribiendo registros estructurales. No recargues ni cierres la página...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    this.submit();
                }
            });
        });
    });

    // 3. Eliminar Respaldo
    document.querySelectorAll('.form-delete-backup').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Eliminar copia de seguridad?',
                text: 'Esta acción borrará de forma permanente el archivo físico del servidor y no podrá recuperarse.',
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'Sí, Eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626', // pc-red
                cancelButtonColor: '#1e293b' // slate-800
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
});
</script>
@endsection