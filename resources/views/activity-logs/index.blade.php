@extends('layouts.app')

@section('breadcrumbs')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-[10px] font-black text-gray-400 hover:text-pc-orange uppercase tracking-widest inline-flex items-center transition-colors">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-700 text-[8px] mx-2"></i>
                <span class="text-[10px] font-black text-pc-orange uppercase tracking-widest">Auditoría Táctica</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Cabecera --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
        <div>
            <h2 class="text-2xl font-black text-pc-blue dark:text-white uppercase tracking-tight flex items-center gap-3">
                <i class="fas fa-shield-check text-pc-orange"></i> Registro de Actividad
            </h2>
            <p class="text-gray-400 dark:text-gray-500 text-[10px] font-bold uppercase tracking-widest mt-1">Trazabilidad completa de operaciones institucionales</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card-pc p-6 mb-8 dark:bg-slate-900 dark:border-slate-800">
        <form method="GET" action="{{ route('activity-logs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="label-pc dark:text-gray-400">Módulo</label>
                <select name="module" class="input-pc text-[10px] uppercase font-black">
                    <option value="">Todos los Módulos</option>
                    <option value="employees" {{ request('module') == 'employees' ? 'selected' : '' }}>Empleados</option>
                    <option value="vacations" {{ request('module') == 'vacations' ? 'selected' : '' }}>Vacaciones</option>
                    <option value="profile" {{ request('module') == 'profile' ? 'selected' : '' }}>Perfil</option>
                    <option value="auth" {{ request('module') == 'auth' ? 'selected' : '' }}>Autenticación</option>
                </select>
            </div>
            <div>
                <label class="label-pc dark:text-gray-400">Acción</label>
                <select name="action" class="input-pc text-[10px] uppercase font-black">
                    <option value="">Todas las Acciones</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Creación</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Actualización</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Eliminación</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Inicio Sesión</option>
                </select>
            </div>
            <div class="md:col-span-1">
                <label class="label-pc dark:text-gray-400">Buscador Global</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por usuario o descripción..." class="input-pc text-[10px] font-bold">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-pc-blue text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase shadow-lg hover:bg-blue-800 transition-all flex-1">Filtrar</button>
                <a href="{{ route('activity-logs.index') }}" class="bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-gray-400 px-6 py-3 rounded-xl font-black text-[10px] uppercase flex items-center justify-center"><i class="fas fa-sync"></i></a>
            </div>
        </form>
    </div>

    {{-- Tabla Técnica de Logs --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-gray-100 dark:shadow-none overflow-hidden border border-gray-100 dark:border-slate-800">
        <div class="overflow-x-auto">
            <table class="w-full table-pc">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800 text-left">
                        <th class="p-5 text-[9px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest">Fecha / Hora</th>
                        <th class="p-5 text-[9px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest">Usuario</th>
                        <th class="p-5 text-[9px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest">Módulo</th>
                        <th class="p-5 text-[9px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest">Acción</th>
                        <th class="p-5 text-[9px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest">Descripción</th>
                        <th class="p-5 text-[9px] font-black text-pc-blue dark:text-gray-300 uppercase tracking-widest">IP / Origen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="p-5 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-gray-900 dark:text-gray-100">{{ $log->created_at->format('d/m/Y') }}</span>
                                    <span class="text-[9px] font-bold text-pc-orange">{{ $log->created_at->format('H:i:s') }}</span>
                                </div>
                            </td>
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-slate-800 flex items-center justify-center text-pc-blue dark:text-pc-orange font-black text-[10px]">
                                        {{ substr($log->user->name, 0, 1) }}
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-700 dark:text-gray-300">{{ $log->user->name }}</span>
                                </div>
                            </td>
                            <td class="p-5">
                                <span class="px-3 py-1 bg-pc-blue/5 dark:bg-pc-blue/10 text-pc-blue dark:text-blue-400 rounded-full text-[8px] font-black uppercase tracking-widest">
                                    {{ $log->module }}
                                </span>
                            </td>
                            <td class="p-5">
                                @php
                                    $actionColor = match($log->action) {
                                        'create' => 'text-emerald-500',
                                        'update' => 'text-pc-orange',
                                        'delete' => 'text-pc-red',
                                        'login' => 'text-blue-500',
                                        default => 'text-gray-500'
                                    };
                                @endphp
                                <span class="{{ $actionColor }} text-[9px] font-black uppercase tracking-widest">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="p-5">
                                <p class="text-[10px] font-medium text-gray-600 dark:text-gray-400 max-w-xs truncate" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </p>
                            </td>
                            <td class="p-5 whitespace-nowrap">
                                <div class="flex items-center gap-2 text-gray-400">
                                    <i class="fas fa-network-wired text-[10px]"></i>
                                    <span class="text-[9px] font-mono tracking-tighter">{{ $log->ip_address }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-gray-400 uppercase text-[10px] font-black">No se registran actividades en el período</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-gray-50/30 dark:bg-slate-800/20 border-t border-gray-50 dark:border-slate-800">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
