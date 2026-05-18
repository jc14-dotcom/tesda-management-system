<?php

namespace App\Console\Commands;

use App\Support\DatabaseBackupRunner;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('backups:run-database {--filename= : Optional zip filename to create}')]
#[Description('Create a database-only backup using the application-safe backup runner')]
class RunDatabaseBackup extends Command
{
    public function handle(DatabaseBackupRunner $runner): int
    {
        try {
            $result = $runner->run($this->option('filename') ?: null);
            $this->info('Backup created: ' . $result['path']);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}