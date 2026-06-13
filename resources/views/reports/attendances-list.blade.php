<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencias</title>
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
        
        /* ── Resumen Ejecutivo ── */
        .kpi-box { border-left: 4px solid #10b981; background: #f0fdf4; padding: 12px 15px; margin-bottom: 20px; color: #065f46; font-size: 9pt; display: flex; gap: 20px; }
        .kpi-item { display: flex; flex-direction: column; }
        .kpi-val { font-size: 14pt; font-weight: 800; }
        .kpi-lbl { font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.8; }

        /* ── Tablas ── */
        table.md-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 8.5pt; }
        table.md-table th { border-bottom: 2px solid #9ca3af; padding: 6px 8px; text-align: left; font-weight: 700; color: #374151; text-transform: uppercase; font-size: 7.5pt; background-color: #f3f4f6; }
        table.md-table td { border-bottom: 1px solid #e5e7eb; padding: 6px 8px; color: #1f2937; vertical-align: top; }
        table.md-table tr:nth-child(even) td { background-color: #f9fafb; }
        
        .no-data { font-style: italic; color: #6b7280; font-size: 8.5pt; }
        .warning-text { color: #d97706; font-weight: bold; font-size: 7.5pt; }

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

    <h1 class="main-title">Reporte Ejecutivo de Asistencias</h1>
    
    <div style="font-family: monospace; font-size: 8pt; color: #4b5563; background-color: #f3f4f6; padding: 4px 8px; border-radius: 4px; margin-bottom: 15px; display: inline-block;">
        <strong>PERÍODO:</strong> {{ strtoupper($period) }} &nbsp;|&nbsp; <strong>FECHA EMISIÓN:</strong> {{ $generatedAt->format('d/m/Y h:i A') }}
    </div>

    {{-- Resumen Ejecutivo KPI --}}
    <table style="width: 100%; border-collapse: collapse; border-left: 4px solid #10b981; background: #f0fdf4; margin-bottom: 20px; border-right: none; border-top: none; border-bottom: none;">
        <tr>
            <td style="width: 33.3%; padding: 12px 15px; border: none; vertical-align: top;">
                <span style="font-size: 14pt; font-weight: 800; color: #065f46; display: block;">{{ $attendances->count() }}</span>
                <span style="font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #065f46; opacity: 0.8; display: block;">Registros Analizados</span>
            </td>
            <td style="width: 33.3%; padding: 12px 15px; border: none; vertical-align: top;">
                <span style="font-size: 14pt; font-weight: 800; color: #d97706; display: block;">{{ $attendances->whereNull('check_out')->count() + $attendances->whereNull('check_in')->count() }}</span>
                <span style="font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #065f46; opacity: 0.8; display: block;">Anomalías / Incompletos</span>
            </td>
            <td style="width: 33.3%; padding: 12px 15px; border: none; vertical-align: top; text-align: right;">
                <span style="font-size: 10pt; font-weight: 800; color: #065f46; display: block; line-height: 1.4; margin-top: 2px;">{{ strtoupper($period) }}</span>
                <span style="font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #065f46; opacity: 0.8; display: block;">Corte Temporal</span>
            </td>
        </tr>
    </table>

    {{-- Tabla de Datos --}}
    <h2 class="block-title">1. Detalle de Control de Marcaje (Orden Cronológico)</h2>
    
    @if($attendances->isEmpty())
        <p class="no-data">No se encontraron registros de asistencia para los parámetros indicados.</p>
    @else
        <table class="md-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="12%">Cédula</th>
                    <th width="33%">Nombre Completo</th>
                    <th width="12%">Fecha</th>
                    <th width="10%">Entrada</th>
                    <th width="10%">Salida</th>
                    <th width="18%">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $i => $att)
                @php
                    $isIncomplete = !$att->check_in || !$att->check_out;
                    $notes = $att->notes;
                    if ($isIncomplete) {
                        $notes = $notes ? "⚠️ Marcaje Incompleto - " . $notes : "⚠️ Marcaje Incompleto";
                    }
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ preg_replace('/([A-Z])-?(\d+)/', '$1-$2', $att->employee->id_number ?? 'S/N') }}</strong></td>
                    <td>{{ $att->employee->full_name ?? '—' }}</td>
                    <td>{{ $att->date ? $att->date->format('d/m/Y') : '—' }}</td>
                    <td>{{ $att->check_in  ? \Carbon\Carbon::parse($att->check_in)->format('H:i')  : '' }}</td>
                    <td>{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '' }}</td>
                    <td>
                        @if($isIncomplete)
                            <span class="warning-text">{{ $notes }}</span>
                        @else
                            {{ $notes }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Firmas Institucionales --}}
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
