<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use App\Models\{
    User, Employee, Department, Position, Rank, Attendance, Leave, Vacation, 
    Deployment, ContingencyPlan, GuardRotation, GuardDuty, ActivityLog, 
    EmployeeDocument, PdfReport, Backup, UserSecurityAnswer
};

class LargeDataLoadSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧹 Iniciando limpieza de la base de datos...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Limpieza de Usuarios
        // Queremos mantener únicamente los usuarios con ID 5, 8, y 12.
        // Y limpiar respuestas de seguridad que no pertenezcan a estos usuarios.
        $keepUserIds = [5, 8, 12];

        // Borrar usuarios no deseados y sus respuestas de seguridad
        User::whereNotIn('id', $keepUserIds)->delete();
        UserSecurityAnswer::whereNotIn('user_id', $keepUserIds)->delete();

        // Asegurarnos de que los roles estén bien creados y asignados
        $adminRole = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $secretariaRole = Role::firstOrCreate(['name' => 'secretaria', 'guard_name' => 'web']);

        // Asignar los roles exactamente como se solicitó
        $adminUser = User::find(8);
        if ($adminUser) {
            $adminUser->syncRoles([$adminRole]);
        }

        $adrianUser = User::find(5);
        if ($adrianUser) {
            $adrianUser->syncRoles([$supervisorRole]);
        }

        $glamisUser = User::find(12);
        if ($glamisUser) {
            $glamisUser->syncRoles([$secretariaRole]);
        }

        // 2. Limpieza de Tablas Transaccionales
        // Queremos limpiar employees (excepto el registro con ID 9, que pertenece a Adrian de Jesus Palacios Brito)
        $keepEmployeeId = 9;
        Employee::whereNotIn('id', [$keepEmployeeId])->delete();

        // Truncar las otras tablas transaccionales
        $tablesToTruncate = [
            'attendances',
            'vacations',
            'leaves',
            'guard_rotations',
            'guard_duties',
            'deployments',
            'deployment_participants',
            'contingency_plans',
            'activity_logs',
            'pdf_reports',
            'employee_documents',
            'backups'
        ];
        foreach ($tablesToTruncate as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('✅ Limpieza completada.');

        // 3. Obtener Catálogos para asignaciones
        $departments = Department::all();
        $positions = Position::all();
        $ranks = Rank::all();

        if ($departments->isEmpty() || $positions->isEmpty() || $ranks->isEmpty()) {
            $this->command->error('Error: Catálogos vacíos. Por favor ejecuta primero los seeders de catálogo.');
            return;
        }

        // Asegurar que el empleado de Adrian Palacios existe y tiene user_id = 5
        $adrianEmployee = Employee::find($keepEmployeeId);
        if ($adrianEmployee) {
            $adrianEmployee->update([
                'user_id' => 5,
                'status' => 'activo'
            ]);
            $this->command->info('👤 Empleado Adrian Palacios (ID 9) actualizado y vinculado a User ID 5.');
        } else {
            // Si por alguna razón no existe, lo creamos con ID 9
            $adrianEmployee = Employee::create([
                'id' => 9,
                'user_id' => 5,
                'first_name' => 'Adrian de Jesus',
                'last_name' => 'Palacios Brito',
                'id_number' => 'V-27297486',
                'employee_code' => 'EMP-009',
                'hired_date' => Carbon::now()->subYears(3),
                'department_id' => $departments->first()->id,
                'position_id' => $positions->first()->id,
                'position' => $positions->first()->name,
                'rank_id' => $ranks->first()->id,
                'gender' => 'masculino',
                'employment_type' => 'fijo',
                'shift_group' => 'A',
                'birth_date' => '1998-05-15',
                'birth_place' => 'Caucagua, Estado Miranda',
                'marital_status' => 'soltero',
                'address' => 'Sector El Clavo, Calle Principal, Casa 5',
                'sector' => 'El Clavo',
                'parish' => 'Parroquia Caucagua',
                'personal_phone' => '04121234567',
                'home_phone' => '02345678901',
                'email' => 'adrian@hotmail.com',
                'blood_type' => 'O+',
                'allergies' => 'Ninguna',
                'emergency_contact_name' => 'María Brito',
                'emergency_contact_phone' => '04147654321',
                'education_level' => 'Universitario',
                'degree' => 'T.S.U. en Informática',
                'institution' => 'IUTM',
                'graduation_year' => 2020,
                'currently_studying' => false,
                'specializations' => 'Desarrollo Web, Gestión de Sistemas',
                'accumulated_days' => 10,
                'employee_type' => 'homologado',
                'institutional_code' => 'PC-ADR-009',
                'status' => 'activo'
            ]);
            $this->command->info('👤 Empleado Adrian Palacios (ID 9) creado y vinculado a User ID 5.');
        }

        // 4. Generación de Empleados (15 más para un total de 16)
        $this->command->info('👥 Generando 15 empleados adicionales con datos completos...');
        $firstNames = ['Juan', 'Pedro', 'Luis', 'Carlos', 'José', 'Miguel', 'María', 'Ana', 'Diana', 'Carmen', 'Laura', 'Patricia', 'Andrés', 'Sonia', 'Jorge'];
        $lastNames = ['Gómez', 'Rodríguez', 'Sánchez', 'Pérez', 'González', 'Martínez', 'López', 'Díaz', 'Hernández', 'Álvarez', 'Flores', 'Romero', 'Ruiz', 'Torres', 'Ramírez'];
        $bloodTypes = ['O+', 'O-', 'A+', 'A-', 'B+', 'AB+'];
        $genders = ['masculino', 'masculino', 'masculino', 'masculino', 'masculino', 'masculino', 'femenino', 'femenino', 'femenino', 'femenino', 'femenino', 'femenino', 'masculino', 'femenino', 'masculino'];
        $maritalStatuses = ['soltero', 'casado', 'divorciado', 'soltero', 'casado', 'soltero', 'casado', 'divorciado', 'soltero', 'casado', 'soltero', 'casado', 'divorciado', 'soltero', 'casado'];
        $employmentTypes = ['fijo', 'contratado', 'comision'];
        $employeeTypes = ['alcaldia', 'gobernacion', 'nacional', 'homologado'];
        $educationLevels = ['Bachiller', 'TSU', 'Universitario', 'Postgrado'];
        $parishes = ['Parroquia Caucagua', 'Parroquia Araguita', 'Parroquia Capaya', 'Parroquia El Café', 'Parroquia Marizapa'];

        $hiredDates = [
            '2015-06-15', '2018-09-22', '2020-01-10', '2022-11-05',
            '2023-05-18', '2012-04-30', '2017-08-12', '2019-12-01',
            '2021-07-19', '2024-02-10', '2020-10-25', '2016-03-14',
            '2019-05-20', '2022-02-28', '2014-11-15'
        ];
        $accumulatedDays = [
            20, 8, 15, 5,
            0, 25, 14, 11,
            6, 2, 18, 22,
            9, 4, 0
        ];
        $statuses = [
            'activo', 'activo', 'activo', 'activo',
            'activo', 'activo', 'activo', 'activo',
            'activo', 'activo', 'activo', 'activo',
            'reposo', 'reposo', 'inactivo'
        ];

        $allEmployees = collect([$adrianEmployee]);

        for ($i = 0; $i < 15; $i++) {
            $fn = $firstNames[$i];
            $ln = $lastNames[$i];
            $gender = $genders[$i];
            $dep = $departments->random();
            $pos = $positions->random();
            $rank = $ranks->random();

            $emp = Employee::create([
                'user_id' => null, // No vinculados a usuarios (solo adrian palacios está vinculado)
                'department_id' => $dep->id,
                'position_id' => $pos->id,
                'position' => $pos->name,
                'rank_id' => $rank->id,
                'first_name' => $fn,
                'last_name' => $ln,
                'id_number' => 'V-' . rand(10000000, 30000000),
                'birth_date' => Carbon::now()->subYears(rand(22, 50))->subMonths(rand(0, 11))->subDays(rand(0, 28))->format('Y-m-d'),
                'birth_place' => 'Caucagua, Estado Miranda',
                'marital_status' => $maritalStatuses[$i],
                'address' => "Calle Principal, Sector " . Str::random(5) . ", Casa " . rand(1, 100),
                'sector' => 'Sector ' . Str::random(5),
                'parish' => $parishes[rand(0, count($parishes)-1)],
                'personal_phone' => '0412' . rand(1000000, 9999999),
                'home_phone' => '0234' . rand(1000000, 9999999),
                'email' => strtolower($fn . '.' . $ln) . '@pcm.gob.ve',
                'blood_type' => $bloodTypes[rand(0, count($bloodTypes)-1)],
                'allergies' => rand(0, 1) ? 'Polvo, humedad' : 'Ninguna conocida',
                'emergency_contact_name' => 'Familiar de ' . $fn,
                'emergency_contact_phone' => '0424' . rand(1000000, 9999999),
                'education_level' => $educationLevels[rand(0, count($educationLevels)-1)],
                'degree' => 'Título de ' . $fn,
                'institution' => 'Institución Educativa Miranda',
                'graduation_year' => rand(2000, 2024),
                'currently_studying' => rand(0, 1) === 1,
                'specializations' => 'Curso de Primeros Auxilios, Rescate Básico',
                'employee_code' => 'EMP-' . str_pad($i + 10, 3, '0', STR_PAD_LEFT),
                'hired_date' => $hiredDates[$i],
                'employment_type' => $employmentTypes[rand(0, count($employmentTypes)-1)],
                'shift_group' => ['A', 'B', 'C', 'D'][$i % 4],
                'status' => $statuses[$i],
                'employee_type' => $employeeTypes[rand(0, count($employeeTypes)-1)],
                'gender' => $gender,
                'institutional_code' => 'PC-EMP-' . str_pad($i + 10, 3, '0', STR_PAD_LEFT),
                'accumulated_days' => $accumulatedDays[$i],
            ]);
            $allEmployees->push($emp);
        }
        $this->command->info('✅ Empleados generados. Total: ' . $allEmployees->count());

        // 5. Generación de Asistencias (Exactamente 16)
        $this->command->info('📋 Generando 16 asistencias distribuidas en varios días...');
        
        $attendanceData = [
            // Hace 7 días
            ['days_ago' => 7, 'emp_idx' => 1, 'in' => '07:20:00', 'out' => '16:30:00', 'status' => 'present'],
            ['days_ago' => 7, 'emp_idx' => 2, 'in' => null,       'out' => null,       'status' => 'absent'],
            
            // Hace 6 días
            ['days_ago' => 6, 'emp_idx' => 12, 'in' => '07:30:00', 'out' => '16:00:00', 'status' => 'present'],
            ['days_ago' => 6, 'emp_idx' => 0,  'in' => '08:10:00', 'out' => '16:00:00', 'status' => 'late'],
            
            // Hace 5 días
            ['days_ago' => 5, 'emp_idx' => 10, 'in' => '07:40:00', 'out' => '16:20:00', 'status' => 'present'],
            ['days_ago' => 5, 'emp_idx' => 11, 'in' => '07:15:00', 'out' => '16:15:00', 'status' => 'present'],
            
            // Hace 4 días
            ['days_ago' => 4, 'emp_idx' => 8, 'in' => '07:35:00', 'out' => '16:10:00', 'status' => 'present'],
            ['days_ago' => 4, 'emp_idx' => 9, 'in' => '07:20:00', 'out' => '16:00:00', 'status' => 'present'],
            
            // Hace 3 días
            ['days_ago' => 3, 'emp_idx' => 6, 'in' => '07:25:00', 'out' => '16:05:00', 'status' => 'present'],
            ['days_ago' => 3, 'emp_idx' => 7, 'in' => '08:15:00', 'out' => '16:00:00', 'status' => 'late'],
            
            // Hace 2 días
            ['days_ago' => 2, 'emp_idx' => 4, 'in' => '07:15:00', 'out' => '16:30:00', 'status' => 'present'],
            ['days_ago' => 2, 'emp_idx' => 5, 'in' => null,       'out' => null,       'status' => 'absent'],
            
            // Hace 1 día (Ayer)
            ['days_ago' => 1, 'emp_idx' => 2, 'in' => '07:30:00', 'out' => '16:00:00', 'status' => 'present'],
            ['days_ago' => 1, 'emp_idx' => 3, 'in' => '07:45:00', 'out' => '16:15:00', 'status' => 'present'],
            
            // Hoy / En jornada
            ['days_ago' => 0, 'emp_idx' => 0, 'in' => '07:20:00', 'out' => null,       'status' => 'present'],
            ['days_ago' => 0, 'emp_idx' => 1, 'in' => '08:05:00', 'out' => null,       'status' => 'late'],
        ];

        foreach ($attendanceData as $data) {
            $emp = $allEmployees[$data['emp_idx']];
            $date = Carbon::today()->subDays($data['days_ago'])->format('Y-m-d');
            Attendance::create([
                'employee_id' => $emp->id,
                'date' => $date,
                'check_in' => $data['in'],
                'check_out' => $data['out'],
                'status' => $data['status'],
            ]);
        }

        // 6. Generación de Vacaciones (Exactamente 16)
        $this->command->info('🏖️  Generando 16 vacaciones...');
        $vacationStatuses = ['pendiente', 'aprobado', 'en_curso', 'finalizado', 'interrumpido', 'reanudado', 'rechazado'];
        for ($i = 0; $i < 16; $i++) {
            $emp = $allEmployees[$i % 16];
            $status = $vacationStatuses[$i % count($vacationStatuses)];
            
            if ($status === 'en_curso') {
                $start = Carbon::now()->subDays(rand(2, 8));
                $end = Carbon::now()->addDays(rand(5, 12));
            } elseif ($status === 'finalizado') {
                $start = Carbon::now()->subDays(rand(25, 45));
                $end = Carbon::now()->subDays(rand(5, 15));
            } elseif ($status === 'pendiente' || $status === 'aprobado') {
                $start = Carbon::now()->addDays(rand(5, 20));
                $end = (clone $start)->addDays(rand(10, 15));
            } else {
                $start = Carbon::now()->addDays(rand(-15, 15));
                $end = (clone $start)->addDays(rand(10, 15));
            }
            
            $daysTaken = $start->diffInDays($end);
            Vacation::create([
                'employee_id' => $emp->id,
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d'),
                'days_taken' => $daysTaken,
                'accumulated_days_used' => rand(0, min(5, $emp->accumulated_days ?? 0)),
                'status' => $status,
                'interruption_reason' => $status === 'interrumpido' ? 'Llamado preventivo por alerta de lluvias' : null,
                'remaining_days' => $status === 'interrumpido' ? rand(2, 5) : 0,
            ]);
        }

        // 7. Generación de Reposos Médicos (Exactamente 16)
        $this->command->info('🏥 Generando 16 reposos médicos...');
        $leaveStatuses = ['pendiente', 'aprobado', 'finalizado', 'rechazado'];
        $conditions = ['Esguince de tobillo', 'Dengue clásico', 'Bronquitis aguda', 'Gastroenteritis', 'Fractura menor'];
        for ($i = 0; $i < 16; $i++) {
            $emp = $allEmployees[$i % 16];
            
            if ($i === 13 || $i === 14) {
                // Empleados en reposo: Reposo activo y aprobado que cubre el día de hoy
                Leave::create([
                    'employee_id' => $emp->id,
                    'start_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                    'duration_value' => 7,
                    'duration_unit' => 'days',
                    'doctor_name' => 'Dr. ' . $firstNames[($i + 3) % 15] . ' ' . $lastNames[($i + 5) % 15],
                    'issuing_institution' => 'Hospital Clínico Universitario',
                    'medical_condition' => $i === 13 ? 'Dengue clásico con complicaciones de plaquetas' : 'Bronquitis aguda con requerimiento de reposo absoluto',
                    'status' => 'aprobado',
                ]);
            } else {
                // Otros empleados: Reposos variados (finalizados, pendientes o rechazados)
                $status = ['finalizado', 'pendiente', 'rechazado'][$i % 3];
                $start = $status === 'finalizado' ? Carbon::now()->subDays(20) : Carbon::now()->addDays(rand(-10, 10));
                $end = (clone $start)->addDays(rand(3, 10));
                Leave::create([
                    'employee_id' => $emp->id,
                    'start_date' => $start->format('Y-m-d'),
                    'end_date' => $end->format('Y-m-d'),
                    'duration_value' => $start->diffInDays($end),
                    'duration_unit' => 'days',
                    'doctor_name' => 'Dr. ' . $firstNames[($i + 3) % 15] . ' ' . $lastNames[($i + 5) % 15],
                    'issuing_institution' => 'Hospital General de Caucagua',
                    'medical_condition' => $conditions[$i % count($conditions)],
                    'status' => $status,
                ]);
            }
        }

        // 8. Guardias Rotativas (Exactamente 16)
        $this->command->info('🔄 Generando 16 guardias rotativas...');
        $rotations = [];
        for ($i = 0; $i < 16; $i++) {
            $empA = $allEmployees[($i * 4) % 16];
            $empB = $allEmployees[($i * 4 + 1) % 16];
            $empC = $allEmployees[($i * 4 + 2) % 16];
            $empD = $allEmployees[($i * 4 + 3) % 16];

            $rotations[] = GuardRotation::create([
                'name' => 'Guardia Rotativa de Prueba ' . ($i + 1),
                'description' => 'Rotación regular para monitoreo ' . ($i + 1),
                'is_active' => $i === 0, // Solo la primera activa
                'employee_a_id' => $empA->id,
                'employee_b_id' => $empB->id,
                'employee_c_id' => $empC->id,
                'employee_d_id' => $empD->id,
            ]);
        }

        // 9. Turnos de Guardia (Exactamente 16)
        $this->command->info('📅 Generando 16 turnos de guardia (guard_duties)...');
        $letters = ['A', 'B', 'C', 'D'];
        $activeEmployees = $allEmployees->filter(fn($e) => $e->status === 'activo')->values();
        
        for ($i = 0; $i < 16; $i++) {
            $rot = $rotations[$i % 16];
            $emp = $allEmployees[$i % 16];
            
            // Asegurar que las guardias activas o futuras se asignen solo a empleados activos
            if ($emp->status !== 'activo') {
                $emp = $activeEmployees[$i % $activeEmployees->count()];
            }
            
            GuardDuty::create([
                'guard_rotation_id' => $rot->id,
                'date' => Carbon::today()->addDays($i)->format('Y-m-d'),
                'letter' => $letters[$i % 4],
                'employee_id' => $emp->id,
                'notes' => 'Observaciones de turno número ' . ($i + 1),
            ]);
        }

        // 10. Despliegues (Exactamente 16)
        $this->command->info('🚨 Generando 16 despliegues...');
        $deploymentStatuses = ['programado', 'en_curso', 'finalizado', 'cancelado'];
        $divisions = ['Búsqueda y Rescate', 'Prevención y Mitigación', 'Materiales Peligrosos', 'Atención Pre-Hospitalaria'];
        $places = ['Sector El Clavo', 'Autopista Gran Mariscal de Ayacucho', 'Sector Marizapa', 'Casco Central Caucagua', 'Urb. Menca de Leoni'];
        for ($i = 0; $i < 16; $i++) {
            $status = $deploymentStatuses[$i % count($deploymentStatuses)];
            
            if ($status === 'en_curso') {
                $start = Carbon::now()->subHours(rand(1, 12));
                $end = null;
                $isIndefinite = true;
            } elseif ($status === 'finalizado') {
                $start = Carbon::now()->subDays(rand(2, 5));
                $end = (clone $start)->addHours(rand(4, 12));
                $isIndefinite = false;
            } elseif ($status === 'programado') {
                $start = Carbon::now()->addDays(rand(1, 3));
                $end = null;
                $isIndefinite = true;
            } else {
                $start = Carbon::now()->subDays(rand(1, 3));
                $end = null;
                $isIndefinite = false;
            }

            $dep = Deployment::create([
                'place' => $places[$i % count($places)] . ' Km ' . rand(1, 30),
                'reason' => 'Incidente de emergencia o simulacro preventivo de nivel ' . rand(1, 5),
                'division' => $divisions[$i % count($divisions)],
                'supervisor_id' => $adrianEmployee->id,
                'start_datetime' => $start,
                'end_datetime' => $end,
                'is_indefinite' => $isIndefinite,
                'status' => $status,
                'notes' => 'Notas de despliegue número ' . ($i + 1),
                'latitude' => 10.2834 + ($i * 0.001),
                'longitude' => -66.6218 - ($i * 0.001),
            ]);

            // Crear participantes (deployment_participants)
            $roles = ['Rescatista', 'Paramédico', 'Logística', 'Comunicaciones'];
            for ($j = 0; $j < 3; $j++) {
                // Si el despliegue está en curso o programado, usar solo empleados activos
                if ($status === 'en_curso' || $status === 'programado') {
                    $participantEmp = $activeEmployees[($i + $j) % $activeEmployees->count()];
                } else {
                    $participantEmp = $allEmployees[($i + $j) % 16];
                }
                
                DB::table('deployment_participants')->insert([
                    'deployment_id' => $dep->id,
                    'employee_id' => $participantEmp->id,
                    'role' => $roles[$j % count($roles)],
                    'division' => $dep->division,
                    'is_leader' => $j === 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 11. Planes de Contingencia (Exactamente 16)
        $this->command->info('🚧 Generando 16 planes de contingencia...');
        for ($i = 0; $i < 16; $i++) {
            ContingencyPlan::create([
                'name' => 'Plan de Alerta Lluvias/Eventos - Fase ' . ($i + 1),
                'start_date' => Carbon::now()->addDays($i * 5)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(($i * 5) + 4)->format('Y-m-d'),
                'description' => 'Protocolo operativo para contingencias y riesgos estacionales número ' . ($i + 1),
            ]);
        }

        // 12. Logs de Actividad (Exactamente 16)
        $this->command->info('📜 Generando 16 logs de actividad...');
        $actions = ['login', 'create_employee', 'update_employee', 'approve_vacation', 'create_leave', 'create_deployment'];
        $modules = ['autenticacion', 'empleados', 'empleados', 'vacaciones', 'reposos', 'despliegues'];
        for ($i = 0; $i < 16; $i++) {
            $userIndex = $i % 3;
            $userId = [8, 5, 12][$userIndex];
            ActivityLog::create([
                'user_id' => $userId,
                'action' => $actions[$i % count($actions)],
                'module' => $modules[$i % count($modules)],
                'description' => 'Log de auditoría simulado para el registro ' . ($i + 1),
                'changes' => null,
                'ip_address' => '192.168.1.' . rand(10, 200),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                'created_at' => Carbon::now()->subHours($i * 6),
            ]);
        }

        // 13. Documentos de Empleados (Exactamente 16)
        $this->command->info('📂 Generando 16 documentos de empleados...');
        $docTypes = ['cedula', 'titulo', 'curriculum', 'medico'];
        for ($i = 0; $i < 16; $i++) {
            $emp = $allEmployees[$i % 16];
            EmployeeDocument::create([
                'employee_id' => $emp->id,
                'title' => 'Documento Requerido - ' . ucfirst($docTypes[$i % 4]),
                'file_path' => 'documents/documento_prueba_' . ($i + 1) . '.pdf',
                'file_name' => 'documento_prueba_' . ($i + 1) . '.pdf',
                'document_type' => $docTypes[$i % 4],
                'description' => 'Expediente digital del funcionario número ' . ($i + 1),
            ]);
        }

        // 14. Reportes PDF (Exactamente 16)
        $this->command->info('📄 Generando 16 reportes PDF...');
        $reportTypes = ['empleados', 'asistencias', 'vacaciones', 'reposos'];
        for ($i = 0; $i < 16; $i++) {
            PdfReport::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'type' => $reportTypes[$i % count($reportTypes)],
                'parameters' => ['division' => 'Operaciones', 'year' => 2026],
                'status' => 'completed',
                'file_path' => 'reports/reporte_carga_' . ($i + 1) . '.pdf',
                'error_message' => null,
            ]);
        }

        // 15. Respaldos (Exactamente 16)
        $this->command->info('💾 Generando 16 respaldos...');
        for ($i = 0; $i < 16; $i++) {
            Backup::create([
                'filename' => 'backup_sistema_' . Carbon::now()->subDays($i)->format('Y_m_d_His') . '.sql',
                'path' => 'backups/backup_sistema_' . ($i + 1) . '.sql',
                'size' => rand(150000, 300000) / 100,
                'created_by' => [8, 5, 12][$i % 3],
            ]);
        }

        $this->command->newLine();
        $this->command->info('╔═══════════════════════════════════════════════════════╗');
        $this->command->info('║  ✅ DATOS DE CARGA DE PRUEBA GENERADOS CON ÉXITO     ║');
        $this->command->info('╠═══════════════════════════════════════════════════════╣');
        $this->command->info('║  Usuarios: 3 (Exactamente 1 por rol)                  ║');
        $this->command->info('║  Empleados: 16 (Todos con datos 100% completos)      ║');
        $this->command->info('║  Asistencias: 16                                      ║');
        $this->command->info('║  Vacaciones: 16                                      ║');
        $this->command->info('║  Reposos Médicos: 16                                  ║');
        $this->command->info('║  Guardias Rotativas: 16                              ║');
        $this->command->info('║  Turnos de Guardia: 16                                ║');
        $this->command->info('║  Despliegues: 16                                      ║');
        $this->command->info('║  Planes Contingencia: 16                              ║');
        $this->command->info('║  Logs de Auditoría: 16                                ║');
        $this->command->info('║  Documentos Empleados: 16                            ║');
        $this->command->info('║  Reportes PDF: 16                                     ║');
        $this->command->info('║  Respaldos: 16                                        ║');
        $this->command->info('╚═══════════════════════════════════════════════════════╝');
    }
}
