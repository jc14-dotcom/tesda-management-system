<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ServeWithQueue extends Command
{
    protected $signature = 'app:serve
                            {--host=127.0.0.1 : The host address to serve the application on}
                            {--port=8000      : The port to serve the application on}
                            {--tries=5        : Max queue worker retry attempts}';

    protected $description = 'Start the development server and queue worker together (stops both on Ctrl+C)';

    public function handle(): int
    {
        $php  = PHP_BINARY;
        $base = base_path();

        // ── 1. Start queue worker as a background process ────────────────────
        $queueProcess = new Process(
            [$php, 'artisan', 'queue:work', '--tries=' . $this->option('tries'), '--sleep=3'],
            $base,
            null,
            null,
            null   // no timeout
        );

        $queueProcess->start(function (string $type, string $buffer): void {
            // Forward queue worker output to storage log only (not cluttering the console)
            file_put_contents(
                storage_path('logs/queue-worker.log'),
                $buffer,
                FILE_APPEND | LOCK_EX
            );
        });

        $this->line('  <fg=green;options=bold>Queue worker</> started (PID: <fg=cyan>' . $queueProcess->getPid() . '</>)');
        $this->line('  Queue output → <fg=gray>storage/logs/queue-worker.log</>');
        $this->newLine();

        // ── 2. Register shutdown: stop queue worker when this process exits ──
        register_shutdown_function(static function () use ($queueProcess): void {
            if ($queueProcess->isRunning()) {
                $queueProcess->stop(3);
            }
        });

        // ── 3. Start the built-in web server (blocks until Ctrl+C) ───────────
        $this->line('  <fg=green;options=bold>Web server</> starting at <fg=cyan>http://' . $this->option('host') . ':' . $this->option('port') . '</>');
        $this->line('  Press <fg=yellow>Ctrl+C</> to stop both services.');
        $this->newLine();

        $this->call('serve', [
            '--host' => $this->option('host'),
            '--port' => $this->option('port'),
        ]);

        // ── 4. Serve exited — stop the queue worker ──────────────────────────
        if ($queueProcess->isRunning()) {
            $this->line('  Stopping queue worker…');
            $queueProcess->stop(3);
        }

        $this->newLine();
        $this->info('All services stopped.');

        return self::SUCCESS;
    }
}
