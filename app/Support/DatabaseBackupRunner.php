<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDO;
use RuntimeException;
use ZipArchive;

class DatabaseBackupRunner
{
    private const DATA_EXCLUDED_TABLES = [
        'cache',
        'cache_locks',
        'failed_jobs',
        'job_batches',
        'jobs',
        'password_reset_tokens',
        'sessions',
    ];

    /**
     * @return array{disk:string,path:string,filename:string,php:string,temp:string,output:string}
     */
    public function run(?string $filename = null): array
    {
        $tempDirectory = $this->backupTempDirectory();
        $filename ??= now()->format('Y-m-d-H-i-s') . '-' . bin2hex(random_bytes(3)) . '.zip';
        $disk = config('backup.backup.destination.disks', ['local'])[0] ?? 'local';
        $expectedPath = trim(config('backup.backup.name', config('app.name')), '/') . '/' . $filename;
        $sqlFilename = pathinfo($filename, PATHINFO_FILENAME) . '.sql';
        $sqlPath = $tempDirectory . DIRECTORY_SEPARATOR . $sqlFilename;
        $zipPath = $tempDirectory . DIRECTORY_SEPARATOR . $filename;

        try {
            $tableCount = $this->writeSqlDump($sqlPath);
            $this->writeZip($zipPath, $sqlPath, $sqlFilename);

            $stream = fopen($zipPath, 'rb');
            if (! is_resource($stream)) {
                throw new RuntimeException('Unable to read generated backup zip file.');
            }

            try {
                if (Storage::disk($disk)->put($expectedPath, $stream) === false) {
                    throw new RuntimeException("Unable to store backup file on disk [{$disk}].");
                }
            } finally {
                fclose($stream);
            }
        } catch (\Throwable $exception) {
            logger()->error('[backup-runner] Failed', [
                'message' => $exception->getMessage(),
                'temp' => $tempDirectory,
            ]);

            throw $exception;
        } finally {
            if (is_file($sqlPath)) {
                unlink($sqlPath);
            }
            if (is_file($zipPath)) {
                unlink($zipPath);
            }
        }

        if (! Storage::disk($disk)->exists($expectedPath)) {
            logger()->error('[backup-runner] Missing backup file after successful process', [
                'disk' => $disk,
                'path' => $expectedPath,
            ]);

            throw new RuntimeException("Backup command finished but the expected file was not created: {$expectedPath}");
        }

        logger()->info('[backup-runner] Created', [
            'disk' => $disk,
            'path' => $expectedPath,
            'tables' => $tableCount,
            'temp' => $tempDirectory,
        ]);

        return [
            'disk' => $disk,
            'path' => $expectedPath,
            'filename' => $filename,
            'php' => PHP_BINARY,
            'temp' => $tempDirectory,
            'output' => "Database backup created with {$tableCount} table(s).",
        ];
    }

    private function writeSqlDump(string $path): int
    {
        $connection = DB::connection();
        $pdo = $connection->getPdo();
        $database = (string) $connection->getDatabaseName();
        $handle = fopen($path, 'wb');

        if (! is_resource($handle)) {
            throw new RuntimeException('Unable to create temporary SQL dump file.');
        }

        try {
            fwrite($handle, "-- Alcatt System database backup\n");
            fwrite($handle, '-- Generated at ' . now()->toDateTimeString() . "\n");
            fwrite($handle, '-- Database: ' . $database . "\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($handle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n");
            fwrite($handle, "SET NAMES utf8mb4;\n\n");

            $tables = $this->baseTables();

            foreach ($tables as $table) {
                $quotedTable = $this->quoteIdentifier($table);
                $createRow = (array) $connection->selectOne('SHOW CREATE TABLE ' . $quotedTable);
                $createSql = $createRow['Create Table'] ?? array_values($createRow)[1] ?? null;

                if (! is_string($createSql) || $createSql === '') {
                    throw new RuntimeException("Unable to read CREATE TABLE statement for [{$table}].");
                }

                fwrite($handle, "-- --------------------------------------------------------\n");
                fwrite($handle, "-- Table structure for {$quotedTable}\n");
                fwrite($handle, "DROP TABLE IF EXISTS {$quotedTable};\n");
                fwrite($handle, $createSql . ";\n\n");

                if (in_array($table, self::DATA_EXCLUDED_TABLES, true)) {
                    fwrite($handle, "-- Data skipped for volatile table {$quotedTable}\n\n");
                    continue;
                }

                $this->writeTableRows($pdo, $handle, $table);
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");

            return count($tables);
        } finally {
            fclose($handle);
        }
    }

    /** @return array<int, string> */
    private function baseTables(): array
    {
        return collect(DB::select('SHOW FULL TABLES'))
            ->map(fn (object $row) => array_values((array) $row))
            ->filter(fn (array $row) => ($row[1] ?? null) === 'BASE TABLE')
            ->map(fn (array $row) => (string) $row[0])
            ->values()
            ->all();
    }

    /** @param resource $handle */
    private function writeTableRows(PDO $pdo, mixed $handle, string $table): void
    {
        $quotedTable = $this->quoteIdentifier($table);
        $statement = $pdo->query('SELECT * FROM ' . $quotedTable);

        if (! $statement) {
            throw new RuntimeException("Unable to read rows from [{$table}].");
        }

        $columns = null;
        $rows = [];
        $rowCount = 0;

        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $columns ??= array_keys($row);
            $rows[] = '(' . implode(', ', array_map(fn ($value) => $this->sqlValue($pdo, $value), array_values($row))) . ')';
            $rowCount++;

            if (count($rows) >= 100) {
                $this->flushInsertRows($handle, $quotedTable, $columns, $rows);
                $rows = [];
            }
        }

        if ($columns !== null && $rows !== []) {
            $this->flushInsertRows($handle, $quotedTable, $columns, $rows);
        }

        fwrite($handle, "-- Dumped {$rowCount} row(s) from {$quotedTable}\n\n");
    }

    /** @param resource $handle */
    private function flushInsertRows(mixed $handle, string $quotedTable, array $columns, array $rows): void
    {
        $quotedColumns = implode(', ', array_map(fn (string $column) => $this->quoteIdentifier($column), $columns));
        fwrite($handle, "INSERT INTO {$quotedTable} ({$quotedColumns}) VALUES\n");
        fwrite($handle, implode(",\n", $rows) . ";\n");
    }

    private function sqlValue(PDO $pdo, mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        $quoted = $pdo->quote((string) $value);

        if ($quoted === false) {
            return "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], (string) $value) . "'";
        }

        return $quoted;
    }

    private function writeZip(string $zipPath, string $sqlPath, string $sqlFilename): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Unable to create backup zip file.');
        }

        try {
            if (! $zip->addFile($sqlPath, $sqlFilename)) {
                throw new RuntimeException('Unable to add SQL dump to backup zip file.');
            }
        } finally {
            $zip->close();
        }
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function backupTempDirectory(): string
    {
        $directory = storage_path('app/backup-temp');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (! is_writable($directory)) {
            throw new RuntimeException("Backup temporary directory is not writable: {$directory}");
        }

        return $directory;
    }
}