<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanTelescope extends Command
{
    protected $signature = 'telescope:clean-all';
    protected $description = 'Truncate all telescope tables to free up database space';

    public function handle()
    {
        $tables = ['telescope_entries', 'telescope_entries_tags', 'telescope_monitoring'];

        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                $count = DB::table($table)->count();
                DB::table($table)->truncate();
                $this->info("Truncated {$table}: {$count} rows deleted.");
            } else {
                $this->warn("Table {$table} does not exist, skipping.");
            }
        }

        $this->info('Telescope tables cleaned.');
        return 0;
    }
}
