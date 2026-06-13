<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WipeDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:wipe-data';

    protected $description = 'Wipes application data but keeps configuration tables and backups.';

    public function handle()
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = [
            'users',
            'employees',
            'attendances',
            'vacations',
            'contingency_plans',
            'leaves',
            'deployments',
            'activity_logs',
            'pdf_reports',
            'notifications',
            'guard_rotations',
            'guard_duties',
            'sessions',
        ];

        foreach ($tables as $table) {
            try {
                \Illuminate\Support\Facades\DB::table($table)->truncate();
                $this->info("Truncated {$table}");
            } catch (\Exception $e) {
                $this->error("Failed to truncate {$table}: " . $e->getMessage());
            }
        }

        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Data wiped successfully.');
    }
}
