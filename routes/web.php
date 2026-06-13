<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ForgotPasswordQuestionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\DeploymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GuardRotationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ==================== PÁGINA DE INICIO ====================
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ==================== DASHBOARD ====================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ==================== RECUPERACIÓN POR PREGUNTAS ====================
Route::get('/forgot-password-questions', [ForgotPasswordQuestionsController::class, 'showVerifyEmailForm'])
    ->name('password.questions.email');
Route::post('/forgot-password-questions', [ForgotPasswordQuestionsController::class, 'verifyEmail'])
    ->name('password.questions.verify-email');
Route::get('/forgot-password-questions/{user}/questions', [ForgotPasswordQuestionsController::class, 'showQuestions'])
    ->name('password.questions.show');
Route::post('/forgot-password-questions/verify-answers', [ForgotPasswordQuestionsController::class, 'verifyAnswers'])
    ->name('password.questions.verify-answers');
Route::get('/forgot-password-questions/reset', [ForgotPasswordQuestionsController::class, 'showResetForm'])
    ->name('password.questions.reset');
Route::post('/forgot-password-questions/reset', [ForgotPasswordQuestionsController::class, 'resetPassword'])
    ->name('password.questions.update');

// ==================== GRUPO ADMIN (solo admin y supervisor) ====================
Route::middleware(['auth', 'role:administrador|supervisor'])->prefix('admin')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('employees', EmployeeController::class);

    // Edición por secciones de empleados
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/{employee}/edit/personal', [EmployeeController::class, 'editPersonal'])->name('edit.personal');
        Route::put('/{employee}/update/personal', [EmployeeController::class, 'updatePersonal'])->name('update.personal');
        Route::get('/{employee}/edit/academic', [EmployeeController::class, 'editAcademic'])->name('edit.academic');
        Route::put('/{employee}/update/academic', [EmployeeController::class, 'updateAcademic'])->name('update.academic');
        Route::get('/{employee}/edit/laboral', [EmployeeController::class, 'editLaboral'])->name('edit.laboral');
        Route::put('/{employee}/update/laboral', [EmployeeController::class, 'updateLaboral'])->name('update.laboral');
        Route::get('/{employee}/edit/documents', [EmployeeController::class, 'editDocuments'])->name('edit.documents');
        Route::post('/{employee}/update/documents', [EmployeeController::class, 'updateDocuments'])->name('update.documents');
    });

    // Auditoría y Trazabilidad
    Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Respaldos
    Route::resource('backups', BackupController::class)->except(['edit', 'update', 'show']);
    Route::post('backups/{backup}/restore', [BackupController::class, 'restore'])->name('backups.restore');
    Route::get('backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::post('backups/upload', [BackupController::class, 'upload'])->name('backups.upload');
});

// ==================== GRUPO AUTENTICADO (todos los usuarios) ====================
Route::middleware('auth')->group(function () {
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/security-questions', [ProfileController::class, 'storeSecurityQuestions'])->name('profile.security-questions.store');

    // Notificaciones
    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Notificaciones marcadas como leídas.');
    })->name('notifications.markAllRead');

    // ==================== ASISTENCIAS ====================
    Route::get('/attendance/search', [AttendanceController::class, 'search'])->name('attendance.search');
    Route::post('/attendance/register', [AttendanceController::class, 'register'])->name('attendance.register');
    Route::get('/server-time', function () {
        return response()->json(['time' => now()->format('H:i:s')]);
    })->name('server.time');

    Route::get('/search-employees', [AttendanceController::class, 'searchEmployees'])->name('search.employees');
    Route::get('/attendances/autocomplete', [AttendanceController::class, 'autocomplete'])->name('attendances.autocomplete');
    Route::resource('attendances', AttendanceController::class)->only(['index', 'show', 'destroy']);
    Route::patch('/attendances/{attendance}/justify', [AttendanceController::class, 'justify'])->name('attendances.justify');
    Route::get('/attendances/export/excel', [AttendanceController::class, 'exportExcel'])->name('attendances.export.excel');
    Route::get('/attendances/export/pdf', [AttendanceController::class, 'exportPdf'])->name('attendances.export.pdf');

    // Autocompletados generales
    Route::get('/employees/autocomplete', [EmployeeController::class, 'autocomplete'])->name('employees.autocomplete');
    Route::get('/departments/autocomplete', [DepartmentController::class, 'autocomplete'])->name('departments.autocomplete');
    Route::get('/users/autocomplete', [UserController::class, 'autocomplete'])->name('users.autocomplete');

    // ==================== MÓDULO DE VACACIONES ====================
    // Rutas personalizadas (deben ir antes del resource para evitar conflictos)
    Route::prefix('vacations')->name('vacations.')->group(function () {
        // Planes de contingencia
        Route::get('/contingencies', [VacationController::class, 'contingencies'])->name('contingencies');
        Route::post('/contingencies', [VacationController::class, 'storeContingency'])->name('contingencies.store');
        Route::put('/contingencies/{contingencyPlan}', [VacationController::class, 'updateContingency'])->name('contingencies.update');
        Route::delete('/contingencies/{contingencyPlan}', [VacationController::class, 'destroyContingency'])->name('contingencies.destroy');
        // Acciones masivas
        Route::post('/mass-approve', [VacationController::class, 'massApprove'])->name('mass.approve');
        Route::post('/mass-reject', [VacationController::class, 'massReject'])->name('mass.reject');
        Route::post('/mass-interrupt', [VacationController::class, 'massInterrupt'])->name('mass.interrupt');
        // Calendario de vacaciones
        Route::get('/calendar', [VacationController::class, 'calendar'])->name('calendar');
        Route::get('/calendar/events', [VacationController::class, 'calendarEvents'])->name('events');
    });

    // Resource principal (CRUD)
    Route::resource('vacations', VacationController::class);
    // Acciones individuales sobre vacaciones
    Route::post('/vacations/{vacation}/approve', [VacationController::class, 'approve'])->name('vacations.approve');
    Route::post('/vacations/{vacation}/reject', [VacationController::class, 'reject'])->name('vacations.reject');
    Route::post('/vacations/{vacation}/interrupt', [VacationController::class, 'interrupt'])->name('vacations.interrupt');
    Route::post('/vacations/{vacation}/resume', [VacationController::class, 'resume'])->name('vacations.resume');
    Route::get('/vacations/{vacation}/resume', [VacationController::class, 'showResumeForm'])->name('vacations.resume.form');
    Route::post('/vacations/{vacation}/finalize', [VacationController::class, 'finalize'])->name('vacations.finalize');

    // ==================== MÓDULO DE REPOSOS ====================
    Route::resource('leaves', LeaveController::class)->parameters(['leaves' => 'leave']);
    Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
    Route::get('/search-employee-by-id', [LeaveController::class, 'searchEmployeeByIdNumber'])->name('search.employee.by.id');

    // ==================== MÓDULO DE DESPLIEGUES ====================
    Route::resource('deployments', DeploymentController::class);
    Route::post('deployments/{deployment}/change-status', [DeploymentController::class, 'changeStatus'])->name('deployments.change-status');
    Route::get('deployments/{deployment}/pdf', [DeploymentController::class, 'pdf'])->name('deployments.pdf');
    Route::get('deployments/widget', [DeploymentController::class, 'widget'])->name('deployments.widget');

    // ==================== REPORTES ====================
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    // Personal
    Route::get('/reports/employees/excel',              [ReportController::class, 'employeesExcel'])->name('reports.employees.excel');
    Route::get('/reports/employees/pdf',                [ReportController::class, 'employeesPdf'])->name('reports.employees.pdf');
    Route::get('/reports/employees/{employee}/profile', [ReportController::class, 'employeeProfile'])->name('reports.employee.profile');
    // Asistencias
    Route::get('/reports/attendances/excel',            [ReportController::class, 'attendancesExcel'])->name('reports.attendances.excel');
    Route::get('/reports/attendances/pdf',              [ReportController::class, 'attendancesPdf'])->name('reports.attendances.pdf');
    // Vacaciones
    Route::get('/reports/vacations/excel',              [ReportController::class, 'vacationsExcel'])->name('reports.vacations.excel');
    // Reposos
    Route::get('/reports/leaves/excel',                 [ReportController::class, 'leavesExcel'])->name('reports.leaves.excel');
    // Guardias
    Route::get('/reports/guards/excel',                 [ReportController::class, 'guardsExcel'])->name('reports.guards.excel');
    
    // Generación asíncrona de reportes PDF
    Route::post('/reports/generate-pdf',                [ReportController::class, 'generatePdf'])->name('reports.generate.pdf');
    Route::get('/reports/pdf-status/{uuid}',           [ReportController::class, 'checkPdfStatus'])->name('reports.pdf.status');
    Route::get('/reports/pdf-download/{uuid}',         [ReportController::class, 'downloadPdf'])->name('reports.pdf.download');

    // ==================== MÓDULO DE GUARDIAS ROTATIVAS ====================
    Route::resource('guard-rotations', GuardRotationController::class);
    Route::prefix('guard-rotations/{guard_rotation}')->name('guard-rotations.')->group(function () {
        Route::get('/calendar', [GuardRotationController::class, 'calendar'])->name('calendar');
        Route::get('/data', [GuardRotationController::class, 'data'])->name('data');
        Route::post('/generate', [GuardRotationController::class, 'generate'])->name('generate');
        Route::post('/update-day', [GuardRotationController::class, 'updateDay'])->name('update-day');
        Route::get('/pdf', [GuardRotationController::class, 'pdf'])->name('pdf');
    });
    Route::get('/guard-duties/today', [GuardRotationController::class, 'todayWidget'])->name('guard-duties.today');

    // ==================== API INTERNA: BÚSQUEDA DE EMPLEADO POR CÉDULA ====================
    // Definida aquí para que herede la sesión web (cookies/auth) y funcione sin tokens en AJAX
    Route::get('/api/employees/by-id/{idNumber}', [EmployeeController::class, 'findByIdNumber'])->name('api.employees.by-id');
    Route::get('/api/employees/autocomplete', [EmployeeController::class, 'autocomplete'])->name('api.employees.autocomplete');

});

// ==================== RUTAS DE AUTENTICACIÓN (BREEZE) ====================
require __DIR__.'/auth.php';