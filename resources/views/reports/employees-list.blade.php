<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Personal</title>
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
        .kpi-box { border-left: 4px solid #2563eb; background: #eff6ff; padding: 12px 15px; margin-bottom: 20px; color: #1e40af; font-size: 9pt; display: flex; gap: 20px; }
        .kpi-item { display: flex; flex-direction: column; }
        .kpi-val { font-size: 14pt; font-weight: 800; }
        .kpi-lbl { font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.8; }

        /* ── Tablas ── */
        table.md-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 8.5pt; }
        table.md-table th { border-bottom: 2px solid #9ca3af; padding: 6px 8px; text-align: left; font-weight: 700; color: #374151; text-transform: uppercase; font-size: 7.5pt; background-color: #f3f4f6; }
        table.md-table td { border-bottom: 1px solid #e5e7eb; padding: 6px 8px; color: #1f2937; vertical-align: top; }
        table.md-table tr:nth-child(even) td { background-color: #f9fafb; }
        
        .no-data { font-style: italic; color: #6b7280; font-size: 8.5pt; }
        .status { font-weight: 700; font-size: 7.5pt; }
        .status-activo { color: #059669; }
        .status-inactivo { color: #dc2626; }
        .status-reposo { color: #d97706; }
        .status-vacaciones { color: #2563eb; }

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

    <h1 class="main-title">Reporte Ejecutivo de Personal</h1>
    
    <div style="font-family: monospace; font-size: 8pt; color: #4b5563; background-color: #f3f4f6; padding: 4px 8px; border-radius: 4px; margin-bottom: 15px; display: inline-block;">
        <strong>TIPO:</strong> PLANTILLA DE PERSONAL &nbsp;|&nbsp; <strong>FECHA EMISIÓN:</strong> {{ $generatedAt->format('d/m/Y h:i A') }}
    </div>

    {{-- Resumen Ejecutivo KPI --}}
    <table style="width: 100%; border-collapse: collapse; border-left: 4px solid #2563eb; background: #eff6ff; margin-bottom: 20px; border-right: none; border-top: none; border-bottom: none;">
        <tr>
            <td style="width: 33.3%; padding: 12px 15px; border: none; vertical-align: top;">
                <span style="font-size: 14pt; font-weight: 800; color: #1e40af; display: block;">{{ $employees->count() }}</span>
                <span style="font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #1e40af; opacity: 0.8; display: block;">Total Funcionarios</span>
            </td>
            <td style="width: 33.3%; padding: 12px 15px; border: none; vertical-align: top;">
                <span style="font-size: 14pt; font-weight: 800; color: #1e40af; display: block;">{{ $employees->where('status', 'activo')->count() }}</span>
                <span style="font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #1e40af; opacity: 0.8; display: block;">Personal Activo</span>
            </td>
            <td style="width: 33.3%; padding: 12px 15px; border: none; vertical-align: top;">
                <span style="font-size: 14pt; font-weight: 800; color: #d97706; display: block;">{{ $employees->where('status', 'reposo')->count() + $employees->where('status', 'vacaciones')->count() }}</span>
                <span style="font-size: 7pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #1e40af; opacity: 0.8; display: block;">Licencias / Reposos</span>
            </td>
        </tr>
    </table>

    {{-- Tabla de Datos --}}
    <h2 class="block-title">1. Listado Oficial de Funcionarios</h2>
    
    @if($employees->isEmpty())
        <p class="no-data">No se encontraron registros de personal para los parámetros indicados.</p>
    @else
        <table class="md-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="12%">Cédula</th>
                    <th width="28%">Nombre Completo</th>
                    <th width="20%">Unidad / Depto.</th>
                    <th width="15%">Cargo</th>
                    <th width="10%">Ingreso</th>
                    <th width="10%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $i => $emp)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ preg_replace('/([A-Z])-?(\d+)/', '$1-$2', $emp->id_number ?? 'S/N') }}</strong></td>
                    <td>{{ $emp->full_name }}</td>
                    <td>{{ $emp->department->name ?? '—' }}</td>
                    <td>{{ $emp->position ?? '—' }}</td>
                    <td>{{ $emp->hired_date ? $emp->hired_date->format('d/m/Y') : '—' }}</td>
                    <td>
                        <span class="status status-{{ strtolower($emp->status) }}">
                            {{ $emp->status === 'reposo' ? 'En Reposo' : ucfirst(strtolower($emp->status)) }}
                        </span>
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
