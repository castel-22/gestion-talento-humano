<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Backup;
use App\Models\ContingencyPlan;
use App\Models\Department;
use App\Models\Deployment;
use App\Models\Employee;
use App\Models\GuardRotation;
use App\Models\Leave;
use App\Models\User;
use App\Models\Vacation;
use App\Policies\AttendancePolicy;
use App\Policies\BackupPolicy;
use App\Policies\ContingencyPlanPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\DeploymentPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\GuardRotationPolicy;
use App\Policies\LeavePolicy;
use App\Policies\UserPolicy;
use App\Policies\VacationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeo explícito de modelos a políticas.
     * Se registran todas las políticas para no depender del auto-discovery.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Attendance::class      => AttendancePolicy::class,
        Backup::class          => BackupPolicy::class,
        ContingencyPlan::class => ContingencyPlanPolicy::class,
        Department::class      => DepartmentPolicy::class,
        Deployment::class      => DeploymentPolicy::class,
        Employee::class        => EmployeePolicy::class,
        GuardRotation::class   => GuardRotationPolicy::class,
        Leave::class           => LeavePolicy::class,
        User::class            => UserPolicy::class,
        Vacation::class        => VacationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}