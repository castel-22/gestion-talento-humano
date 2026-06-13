@extends('layouts.app')

@section('breadcrumbs')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="text-[10px] font-black text-gray-400 hover:text-pc-orange uppercase tracking-widest inline-flex items-center transition-colors">
                <i class="fas fa-home mr-2"></i> Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-700 text-[8px] mx-2"></i>
                <a href="{{ route('users.index') }}" class="text-[10px] font-black text-gray-400 hover:text-pc-orange uppercase tracking-widest transition-colors">Control de Operadores</a>
            </div>
        </li>
        <li aria-current="page">
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-700 text-[8px] mx-2"></i>
                <span class="text-[10px] font-black text-pc-orange uppercase tracking-widest">Expediente de Agente</span>
            </div>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-pc p-8 dark:bg-slate-900 dark:border-slate-800">
        <div class="flex flex-col md:flex-row items-center gap-8 mb-10 pb-10 border-b border-gray-100 dark:border-slate-800">
            <div class="w-24 h-24 rounded-3xl bg-pc-blue text-white flex items-center justify-center text-4xl font-black shadow-2xl shadow-blue-900/20">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div class="text-center md:text-left">
                <h2 class="text-2xl font-black text-pc-blue dark:text-white uppercase tracking-tight mb-2">{{ $user->name }}</h2>
                <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-4">
                    @foreach($user->roles as $role)
                        <span class="bg-pc-orange/10 text-pc-orange text-[9px] font-black uppercase tracking-[0.2em] px-4 py-1.5 rounded-full border border-pc-orange/20 shadow-sm shadow-orange-500/5">
                            <i class="fas fa-shield-alt mr-1"></i> {{ $role->name }}
                        </span>
                    @endforeach
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em] opacity-70">
                    <i class="fas fa-fingerprint mr-1"></i> ID de Registro: #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="space-y-8">
                <div>
                    <h3 class="text-[10px] font-black text-pc-blue dark:text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-pc-orange"></i> Datos de Acceso
                    </h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-2xl border border-gray-100 dark:border-slate-800">
                            <p class="text-[8px] font-black text-gray-400 uppercase mb-1">Correo Institucional</p>
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $user->email }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-2xl border border-gray-100 dark:border-slate-800">
                            <p class="text-[8px] font-black text-gray-400 uppercase mb-1">Estado de Seguridad</p>
                            <p class="text-xs font-bold text-green-500 uppercase flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Verificado / Activo
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-[10px] font-black text-pc-blue dark:text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-shield-virus text-pc-orange"></i> Protocolos de Respaldo
                </h3>
                <div class="space-y-3">
                    @foreach($user->securityAnswers as $index => $answer)
                        <div class="p-4 bg-black/5 dark:bg-slate-800/30 rounded-2xl border border-white/5 group hover:border-pc-orange/30 transition-all">
                            <p class="text-[8px] font-black text-pc-orange/50 uppercase mb-1">Desafío {{ $index + 1 }}</p>
                            <p class="text-[10px] font-bold text-gray-600 dark:text-gray-400 italic mb-2">{{ $answer->question->question }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-[8px] font-black text-gray-400 dark:text-slate-600 uppercase">Respuesta:</span>
                                <span class="text-xs text-gray-400 dark:text-slate-700">••••••••••••••</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-gray-100 dark:border-slate-800 flex justify-between items-center">
            <a href="{{ route('users.index') }}" class="px-8 py-3 text-[10px] font-black text-gray-500 hover:text-pc-blue uppercase tracking-widest transition-colors">
                <i class="fas fa-chevron-left mr-2"></i> Volver al Panel
            </a>
            @can('update', $user)
                <a href="{{ route('users.edit', $user) }}" class="btn-pc-secondary px-8 py-3 shadow-xl shadow-blue-900/20">
                    <i class="fas fa-cog mr-2"></i> Reconfigurar
                </a>
            @endcan
        </div>
    </div>
</div>
@endsection