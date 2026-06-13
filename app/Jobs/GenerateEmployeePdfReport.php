<?php

namespace App\Jobs;

use App\Models\PdfReport;
use App\Models\Employee;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GenerateEmployeePdfReport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $report;

    /**
     * Create a new job instance.
     */
    public function __construct(PdfReport $report)
    {
        $this->report = $report;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Aumentar la memoria para prevenir Memory Limits con DomPDF en miles de registros
        ini_set('memory_limit', '2G');
        set_time_limit(3600);

        $this->report->update([
            'status' => 'processing',
        ]);

        try {
            $generatedAt = now();
            $pdf = null;

            if ($this->report->type === 'employee_profile') {
                $employeeId = $this->report->parameters['employee_id'] ?? null;
                $employee = Employee::findOrFail($employeeId);
                $employee->load(['department', 'rank', 'documents', 'leaves', 'vacations', 'deployments']);

                $balance = $employee->getVacationBalance();

                $vacations = $employee->vacations()
                    ->orderBy('start_date', 'desc')
                    ->limit(10)
                    ->get();

                $leaves = $employee->leaves()
                    ->orderBy('start_date', 'desc')
                    ->limit(10)
                    ->get();

                $attendances = $employee->attendances()
                    ->where('date', '>=', now()->subMonths(3)->toDateString())
                    ->orderBy('date', 'desc')
                    ->get();

                $deployments = $employee->deployments()
                    ->orderBy('deployments.start_datetime', 'desc')
                    ->limit(5)
                    ->get();

                $pdf = Pdf::loadView('reports.employee-profile', compact(
                    'employee', 'balance', 'vacations', 'leaves', 'attendances', 'deployments', 'generatedAt'
                ))->setPaper('a4', 'portrait');

            } elseif ($this->report->type === 'employees_list') {
                $query = Employee::with(['department', 'rank']);
                
                $departmentId = $this->report->parameters['department_id'] ?? null;
                if (!empty($departmentId)) {
                    $query->where('department_id', $departmentId);
                }
                
                $status = $this->report->parameters['status'] ?? null;
                if (!empty($status)) {
                    $query->where('status', $status);
                }

                $employees = $query->orderBy('first_name')->get();

                $pdf = Pdf::loadView('reports.employees-list', compact('employees', 'generatedAt'))
                    ->setPaper('a4', 'landscape');

            } elseif ($this->report->type === 'attendances_list') {
                $query = Attendance::with('employee');

                $periodParam = $this->report->parameters['period'] ?? null;
                if (!empty($periodParam)) {
                    $date = Carbon::parse($periodParam);
                    $query->whereYear('date', $date->year)->whereMonth('date', $date->month);
                }
                
                $employeeId = $this->report->parameters['employee_id'] ?? null;
                if (!empty($employeeId)) {
                    $query->where('employee_id', $employeeId);
                }

                $attendances = $query->orderBy('date', 'desc')->get();
                $period = !empty($periodParam) ? Carbon::parse($periodParam)->translatedFormat('F Y') : 'Todos los períodos';

                $pdf = Pdf::loadView('reports.attendances-list', compact('attendances', 'generatedAt', 'period'))
                    ->setPaper('a4', 'landscape');
            }

            if (!$pdf) {
                throw new \Exception("Tipo de reporte no soportado: " . $this->report->type);
            }

            // Guardar en Storage public disk
            $fileName = 'reports/' . $this->report->uuid . '.pdf';
            Storage::disk('public')->put($fileName, $pdf->output());

            $this->report->update([
                'status' => 'completed',
                'file_path' => $fileName,
            ]);

        } catch (\Throwable $e) {
            Log::error("Error generando PDF asíncrono para el reporte {$this->report->uuid}: " . $e->getMessage(), [
                'exception' => $e
            ]);

            $this->report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
