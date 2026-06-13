<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hoja de Vida Institucional — {{ $employee->full_name }}</title>
    <style>
        /* ── Estilo Ejecutivo Minimalista ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 9pt; color: #1f2937; background: #fff; line-height: 1.4; padding: 20px; }

        /* ── Header Institucional ── */
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 2px solid #0B3B5E; padding-bottom: 10px; margin-bottom: 15px; border: none; }
        .header-table td { border: none; padding: 0; vertical-align: middle; }
        .header-text { font-size: 9pt; font-weight: bold; color: #0B3B5E; line-height: 1.3; text-transform: uppercase; text-align: center; }

        /* ── Tipografía ── */
        h1.main-title { font-size: 16pt; font-weight: 800; color: #111827; margin-bottom: 15px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
        h2.block-title { font-size: 12pt; font-weight: 700; color: #1f2937; margin-top: 25px; margin-bottom: 10px; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
        h3.sub-title { font-size: 10pt; font-weight: 700; color: #374151; margin-top: 15px; margin-bottom: 8px; }
        hr { border: 0; border-bottom: 1px solid #e5e7eb; margin: 15px 0; }

        /* ── Listas de Datos (Bloques 1 y 2) ── */
        .data-list { width: 100%; display: table; border-spacing: 0 4px; }
        .data-row { display: table-row; }
        .data-label { display: table-cell; width: 30%; font-weight: 700; color: #374151; vertical-align: top; padding-right: 10px; }
        .data-label::after { content: ":"; }
        .data-value { display: table-cell; width: 70%; color: #111827; }

        /* ── Balance de Tiempo (Bloque 3) ── */
        .balance-table { width: 100%; border-collapse: collapse; border: 1px solid #d1d5db; background: #f9fafb; margin-bottom: 15px; border-radius: 4px; }
        .balance-cell { width: 25%; text-align: center; padding: 12px; border-right: 1px solid #e5e7eb; }
        .balance-cell:last-child { border-right: none; }
        .balance-num { font-size: 16pt; font-weight: 800; color: #111827; display: block; }
        .balance-text { font-size: 7.5pt; font-weight: 700; color: #4b5563; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px; display: block; }

        /* ── Tablas (Bloque 4) ── */
        table.md-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 8.5pt; }
        table.md-table th { border-bottom: 2px solid #9ca3af; padding: 6px 8px; text-align: left; font-weight: 700; color: #374151; background-color: #f3f4f6; text-transform: uppercase; font-size: 7.5pt; }
        table.md-table td { border-bottom: 1px solid #e5e7eb; padding: 6px 8px; color: #1f2937; vertical-align: top; }
        table.md-table tr:nth-child(even) td { background-color: #f9fafb; }
        
        /* ── Utilidades ── */
        .status { font-weight: 700; padding: 1px 6px; border-radius: 3px; font-size: 7.5pt; border: 1px solid currentColor; }
        .status-activo { color: #059669; }
        .status-inactivo { color: #dc2626; }
        .status-en_curso { color: #2563eb; }
        .status-aprobado { color: #059669; }
        .status-finalizado { color: #4b5563; }
        .status-programado { color: #d97706; }
        .status-interrumpido { color: #b91c1c; }
        .no-data { font-style: italic; color: #6b7280; font-size: 8.5pt; }

        /* ── Firmas ── */
        .signatures { margin-top: 50px; display: flex; justify-content: space-between; }
        .sign-box { width: 45%; text-align: center; }
        .sign-line { border-top: 1px solid #111827; margin-top: 40px; padding-top: 5px; font-weight: 700; font-size: 8pt; text-transform: uppercase; }
    </style>
</head>
<body>

    {{-- Encabezado Institucional --}}
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: left;">
                <img src="{{ public_path('images/logo_pc.png') }}" style="height: 55px; width: auto;">
            </td>
            <td style="width: 70%;">
                <div class="header-text">
                    REPÚBLICA BOLIVARIANA DE VENEZUELA<br>
                    GOBERNACIÓN DEL ESTADO BOLÍVAR<br>
                    SECRETARÍA DE SEGURIDAD CIUDADANA<br>
                    DIRECCIÓN ESTADAL DE PROTECCIÓN CIVIL Y ADMINISTRACIÓN DE DESASTRES
                </div>
            </td>
            <td style="width: 15%; text-align: right;">
                <img src="{{ public_path('images/logo_ciudad_bolivar.png') }}" style="height: 55px; width: auto;">
            </td>
        </tr>
    </table>

    <h1 class="main-title">Ficha Ejecutiva de Personal</h1>
    
    <div style="font-family: monospace; font-size: 8pt; color: #4b5563; background-color: #f3f4f6; padding: 4px 8px; border-radius: 4px; margin-bottom: 15px; display: inline-block;">
        <strong>EXPEDIENTE:</strong> {{ $employee->employee_code ?? 'S/N' }} &nbsp;|&nbsp; <strong>FECHA EMISIÓN:</strong> {{ $generatedAt->format('d/m/Y h:i A') }}
    </div>

    {{-- BLOQUE 1: IDENTIFICACIÓN --}}
    <h2 class="block-title">1. Identificación del Funcionario</h2>
    <div class="data-list">
        <div class="data-row">
            <div class="data-label">Nombre Completo</div>
            <div class="data-value"><strong>{{ strtoupper($employee->full_name) }}</strong></div>
        </div>
        <div class="data-row">
            <div class="data-label">Cédula de Identidad</div>
            <div class="data-value">{{ $employee->id_number }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Nacimiento</div>
            <div class="data-value">{{ $employee->birth_date ? $employee->birth_date->format('d/m/Y') : '—' }} ({{ $employee->birth_place ?? '—' }})</div>
        </div>
        <div class="data-row">
            <div class="data-label">Estado Civil / Tipo Sangre</div>
            <div class="data-value">{{ ucfirst($employee->marital_status ?? '—') }} &nbsp;|&nbsp; {{ $employee->blood_type ?? '—' }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Dirección Residencial</div>
            <div class="data-value">{{ $employee->address ?? '—' }}{{ $employee->sector ? ', ' . $employee->sector : '' }}{{ $employee->parish ? ', ' . $employee->parish : '' }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Contacto Personal</div>
            <div class="data-value">{{ $employee->personal_phone ?? '—' }} &nbsp;|&nbsp; {{ $employee->email ?? '—' }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Contacto de Emergencia</div>
            <div class="data-value">{{ $employee->emergency_contact_name ?? '—' }} ({{ $employee->emergency_contact_phone ?? '—' }})</div>
        </div>
    </div>

    {{-- BLOQUE 2: PERFIL INSTITUCIONAL --}}
    <h2 class="block-title">2. Perfil Profesional e Institucional</h2>
    <div class="data-list">
        <div class="data-row">
            <div class="data-label">Unidad / Departamento</div>
            <div class="data-value"><strong>{{ $employee->department->name ?? '—' }}</strong></div>
        </div>
        <div class="data-row">
            <div class="data-label">Cargo Administrativo</div>
            <div class="data-value">{{ $employee->position ?? '—' }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Estado Laboral</div>
            <div class="data-value">
                <span class="status status-{{ strtolower($employee->status) }}">{{ ucfirst($employee->status ?? '—') }}</span>
                &nbsp;|&nbsp; {{ ucfirst($employee->employment_type ?? '—') }}
            </div>
        </div>
        <div class="data-row">
            <div class="data-label">Fecha de Ingreso</div>
            <div class="data-value">{{ $employee->hired_date ? $employee->hired_date->format('d/m/Y') : '—' }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Tiempo de Servicio</div>
            <div class="data-value">
                @if($employee->hired_date)
                    @php
                        $years = $employee->hired_date->diffInYears(now());
                        $months = $employee->hired_date->copy()->addYears($years)->diffInMonths(now());
                    @endphp
                    <strong>{{ $years }} años y {{ $months }} meses</strong>
                @else
                    —
                @endif
            </div>
        </div>
        <div class="data-row">
            <div class="data-label">Formación Académica</div>
            <div class="data-value">{{ $employee->education_level ?? '—' }} en {{ $employee->degree ?? '—' }}</div>
        </div>
    </div>

    {{-- BLOQUE 3: BALANCE DE TIEMPO LIBRE --}}
    <h2 class="block-title">3. Balance de Tiempo Libre (Vacaciones)</h2>
    <table class="balance-table">
        <tr>
            <td class="balance-cell">
                <span class="balance-num">{{ $balance['base_days'] }}</span>
                <span class="balance-text">Días Base (Anual)</span>
            </td>
            <td class="balance-cell">
                <span class="balance-num">{{ $balance['regular_available'] }}</span>
                <span class="balance-text">Saldo Regular</span>
            </td>
            <td class="balance-cell">
                <span class="balance-num" style="color: #dc2626;">{{ $balance['accumulated_available'] }}</span>
                <span class="balance-text">Días Vencidos</span>
            </td>
            <td class="balance-cell">
                <span class="balance-num" style="color: #059669;">{{ $balance['total_available'] }}</span>
                <span class="balance-text">Total Disponible</span>
            </td>
        </tr>
    </table>

    {{-- BLOQUE 4: REGISTRO OPERATIVO --}}
    <h2 class="block-title">4. Registro de Actividad Operativa</h2>

    {{-- 4.1 Despliegues --}}
    <h3 class="sub-title">Últimos Despliegues en Campo</h3>
    @if($deployments->isEmpty())
        <p class="no-data">No se registran despliegues operativos recientes.</p>
    @else
        <table class="md-table">
            <thead>
                <tr>
                    <th width="40%">Motivo / Razón</th>
                    <th width="20%">Inicio</th>
                    <th width="20%">Fin</th>
                    <th width="20%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deployments as $dep)
                <tr>
                    <td><strong>{{ $dep->place ?? $dep->reason ?? 'Despliegue General' }}</strong></td>
                    <td>{{ $dep->start_datetime ? \Carbon\Carbon::parse($dep->start_datetime)->format('d/m/Y H:i') : '—' }}</td>
                    <td>{{ $dep->end_datetime   ? \Carbon\Carbon::parse($dep->end_datetime)->format('d/m/Y H:i')   : ($dep->is_indefinite ? 'Indefinido' : '—') }}</td>
                    <td><span class="status status-{{ strtolower($dep->status ?? '') }}">{{ ucfirst(str_replace('_', ' ', $dep->status ?? '—')) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- 4.2 Reposos --}}
    <h3 class="sub-title">Historial de Reposos Médicos</h3>
    @if($leaves->isEmpty())
        <p class="no-data">No se registran reposos médicos en el expediente.</p>
    @else
        <table class="md-table">
            <thead>
                <tr>
                    <th width="25%">Desde - Hasta</th>
                    <th width="15%">Días</th>
                    <th width="30%">Médico Tratante</th>
                    <th width="30%">Institución</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaves as $lv)
                <tr>
                    <td>{{ $lv->start_date ? \Carbon\Carbon::parse($lv->start_date)->format('d/m/Y') : '—' }} a {{ $lv->end_date ? \Carbon\Carbon::parse($lv->end_date)->format('d/m/Y') : '—' }}</td>
                    <td><strong>{{ ($lv->start_date && $lv->end_date) ? \Carbon\Carbon::parse($lv->start_date)->diffInDays(\Carbon\Carbon::parse($lv->end_date)) + 1 : '—' }}</strong></td>
                    <td>{{ $lv->doctor_name ?? '—' }}</td>
                    <td>{{ $lv->issuing_institution ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- 4.3 Vacaciones --}}
    <h3 class="sub-title">Historial de Vacaciones Disfrutadas</h3>
    @if($vacations->isEmpty())
        <p class="no-data">No se registran solicitudes de vacaciones.</p>
    @else
        <table class="md-table">
            <thead>
                <tr>
                    <th width="25%">Período Disfrutado</th>
                    <th width="20%">Regulares</th>
                    <th width="20%">Vencidos</th>
                    <th width="15%">Total</th>
                    <th width="20%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vacations as $vac)
                <tr>
                    <td>{{ $vac->start_date ? \Carbon\Carbon::parse($vac->start_date)->format('d/m/Y') : '—' }} a {{ $vac->end_date ? \Carbon\Carbon::parse($vac->end_date)->format('d/m/Y') : '—' }}</td>
                    <td>{{ $vac->regular_days_taken ?? 0 }} días</td>
                    <td>{{ $vac->accumulated_days_used ?? 0 }} días</td>
                    <td><strong>{{ $vac->days_taken }}</strong></td>
                    <td><span class="status status-{{ strtolower($vac->status) }}">{{ ucfirst(str_replace('_', ' ', $vac->status)) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- 4.4 Asistencias --}}
    <h3 class="sub-title">Control de Asistencia (Últimos Registros)</h3>
    @if($attendances->isEmpty())
        <p class="no-data">No se registran asistencias en los últimos 3 meses.</p>
    @else
        <table class="md-table" style="width: 70%;">
            <thead>
                <tr>
                    <th width="30%">Fecha</th>
                    <th width="20%">Entrada</th>
                    <th width="20%">Salida</th>
                    <th width="30%">Notas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances->take(7) as $att)
                <tr>
                    <td><strong>{{ $att->date ? $att->date->format('d/m/Y') : '—' }}</strong></td>
                    <td>{{ $att->check_in  ? \Carbon\Carbon::parse($att->check_in)->format('H:i')  : '—' }}</td>
                    <td>{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '—' }}</td>
                    <td style="color: #6b7280; font-size: 7.5pt;">{{ $att->notes ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- FIRMAS INSTITUCIONALES --}}
    <table style="width: 100%; margin-top: 50px; border-collapse: collapse; border: none;">
        <tr>
            <td style="width: 45%; text-align: center; vertical-align: top; border: none;">
                <div style="border-top: 1px solid #111827; margin-top: 40px; padding-top: 5px; font-weight: 700; font-size: 8pt; text-transform: uppercase;">Director(a) de Protección Civil</div>
                <div style="font-size: 7pt; color: #6b7280; margin-top: 3px;">Firma y Sello</div>
            </td>
            <td style="width: 10%; border: none;"></td>
            <td style="width: 45%; text-align: center; vertical-align: top; border: none;">
                <div style="border-top: 1px solid #111827; margin-top: 40px; padding-top: 5px; font-weight: 700; font-size: 8pt; text-transform: uppercase;">Jefe(a) de División de Personal</div>
                <div style="font-size: 7pt; color: #6b7280; margin-top: 3px;">Firma y Sello</div>
            </td>
        </tr>
    </table>

</body>
</html>
