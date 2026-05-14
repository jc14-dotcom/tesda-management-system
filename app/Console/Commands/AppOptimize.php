<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Warms or clears all application caches in the correct order.
 *
 * Usage:
 *   php artisan app:optimize          — build config, route, and view caches
 *   php artisan app:optimize --clear  — clear all caches (use before a deploy)
 *
 * Run this command every time you deploy or change views/config/routes.
 * On Windows + Laragon, compilation overhead is the primary cause of slow
 * first-request times. This command eliminates that overhead.
 */
class AppOptimize extends Command
{
    protected $signature = 'app:optimize
        {--clear : Clear all caches instead of warming them}';

    protected $description = 'Warm (or clear) config, route, view, and event caches';

    public function handle(): int
    {
        if ($this->option('clear')) {
            $this->info('Clearing all caches…');
            $this->call('view:clear');
            $this->call('config:clear');
            $this->call('route:clear');
            $this->call('event:clear');
            $this->call('cache:clear');
            $this->newLine();
            $this->info('✓ All caches cleared.');
            return self::SUCCESS;
        }

        $this->info('Warming caches…');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');
        $this->newLine();
        $this->info('✓ Done. Config, routes, and views are now pre-compiled.');
        $this->line('  Run <comment>php artisan app:optimize --clear</comment> to reset before making changes.');

        return self::SUCCESS;
    }
}
