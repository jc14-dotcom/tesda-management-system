<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\DatabaseBackupRunner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use ZipArchive;

class BackupController extends Controller
{
    private const FREQ_KEY     = 'backup_schedule_frequency';
    private const TIME_KEY     = 'backup_schedule_time';
    private const WEEKDAY_KEY  = 'backup_schedule_weekday';
    private const MONTHDAY_KEY = 'backup_schedule_monthday';
    private const TRANSIENT_TABLES = [
        'cache',
        'cache_locks',
        'failed_jobs',
        'job_batches',
        'jobs',
        'password_reset_tokens',
        'sessions',
    ];

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $backupName = config('backup.backup.name', config('app.name'));
        $disk       = config('backup.backup.destination.disks', ['local'])[0] ?? 'local';
        $path       = $backupName;

        $backups = [];

        // allFiles() is recursive — catches backups stored in date sub-folders.
        // Use try-catch instead of exists() because exists() only checks files,
        // not directories; on a fresh install the directory doesn't exist yet.
        try {
            foreach (Storage::disk($disk)->allFiles($path) as $file) {
                if (str_ends_with($file, '.zip')) {
                    $backups[] = [
                        'name'     => basename($file),
                        'size'     => Storage::disk($disk)->size($file),
                        'modified' => Storage::disk($disk)->lastModified($file),
                        'path'     => $file,
                        'disk'     => $disk,
                    ];
                }
            }
            usort($backups, fn ($a, $b) => $b['modified'] - $a['modified']);
        } catch (\Throwable) {
            // Backup directory doesn't exist yet — no backups created.
        }

        // Database info for the header card
        try {
            $dbConn = config('database.default');
            $dbName = config("database.connections.{$dbConn}.database");
            $sizeRow = DB::select('SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS sz FROM information_schema.tables WHERE table_schema = DATABASE()');
            $dbSizeMb = $sizeRow[0]->sz ?? 0;
        } catch (\Throwable) {
            $dbName   = config('database.connections.'.config('database.default').'.database', 'N/A');
            $dbSizeMb = null;
        }

        $totalSize = array_sum(array_column($backups, 'size'));

        return view('admin.backups.index', [
            'backups'   => $backups,
            'disk'      => $disk,
            'dbName'    => $dbName,
            'dbSizeMb'  => $dbSizeMb,
            'stats'     => [
                'count'        => count($backups),
                'lastModified' => $backups[0]['modified'] ?? null,
                'totalSize'    => $totalSize,
            ],
            'schedule'  => [
                'frequency' => Setting::get(self::FREQ_KEY, 'disabled'),
                'time'      => Setting::get(self::TIME_KEY, '02:00'),
                'weekday'   => Setting::get(self::WEEKDAY_KEY, '1'),
                'monthday'  => Setting::get(self::MONTHDAY_KEY, '1'),
            ],
        ]);
    }

    // ─── Run ──────────────────────────────────────────────────────────────────

    public function run(Request $request, DatabaseBackupRunner $runner)
    {
        try {
            $runner->run();

            return redirect()->route('admin.backups.index')->with('status', 'backup-success');
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')
                ->with('status', 'backup-failed')
                ->with('backup_error', $e->getMessage());
        }
    }

    // ─── Download ─────────────────────────────────────────────────────────────

    public function download(Request $request)
    {
        $disk = $request->query('disk', 'local');
        $path = $request->query('path');

        if (! $path || str_contains($path, '..') || ! Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk($disk);

        return $storage->download($path, basename($path));
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function destroy(Request $request)
    {
        $disk = $request->query('disk', 'local');
        $path = $request->query('path');

        if (! $path || str_contains($path, '..') || ! Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        Storage::disk($disk)->delete($path);

        return redirect()->route('admin.backups.index')->with('status', 'backup-deleted');
    }

    // ─── Restore from existing backup ─────────────────────────────────────────

    public function restore(Request $request)
    {
        $disk = $request->input('disk', 'local');
        $path = $request->input('path');

        if (! $path || str_contains($path, '..') || ! Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bk_restore_' . uniqid();

        try {
            mkdir($tempDir, 0755, true);

            $zipPath = $tempDir . DIRECTORY_SEPARATOR . 'backup.zip';
            file_put_contents($zipPath, Storage::disk($disk)->get($path));

            return $this->restoreFromZip($request, $tempDir, $zipPath);

        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')
                ->with('status', 'backup-restore-failed')
                ->with('backup_error', $e->getMessage());
        } finally {
            $this->rmdirRecursive($tempDir);
        }
    }

    // ─── Restore from uploaded ZIP ────────────────────────────────────────────

    public function restoreFromUpload(Request $request)
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'mimes:zip', 'max:204800'], // 200 MB max
        ]);

        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bk_restore_' . uniqid();

        try {
            mkdir($tempDir, 0755, true);
            $request->file('backup_file')->move($tempDir, 'backup.zip');
            $zipPath = $tempDir . DIRECTORY_SEPARATOR . 'backup.zip';

            return $this->restoreFromZip($request, $tempDir, $zipPath);

        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')
                ->with('status', 'backup-restore-failed')
                ->with('backup_error', $e->getMessage());
        } finally {
            $this->rmdirRecursive($tempDir);
        }
    }

    // ─── Shared restore logic ─────────────────────────────────────────────────

    private function restoreFromZip(Request $request, string $tempDir, string $zipPath): mixed
    {
        $zip    = new ZipArchive();
        $result = $zip->open($zipPath);
        if ($result !== true) {
            throw new \RuntimeException('Cannot open backup archive (ZipArchive code ' . $result . ').');
        }
        $zip->extractTo($tempDir);
        $zip->close();
        unlink($zipPath);

        $sqlFile = $this->findSqlDump($tempDir);
        if (! $sqlFile) {
            throw new \RuntimeException('No database dump found inside this backup file.');
        }

        if (str_ends_with($sqlFile, '.gz')) {
            $sqlFile = $this->gunzip($sqlFile);
        }

        $this->restoreSqlDump($sqlFile);
        $this->clearTransientTables();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Database restored from backup. Please sign in again.')
            ->with('restore_success', 'Database restored from backup. Please sign in again.');
    }

    // ─── Save Schedule ────────────────────────────────────────────────────────

    public function saveSchedule(Request $request)
    {
        $data = $request->validate([
            'frequency' => ['required', 'in:disabled,daily,weekly,monthly,quarterly,yearly'],
            'time'      => ['nullable', 'regex:/^\d{2}:\d{2}$/'],
            'weekday'   => ['nullable', 'integer', 'min:1', 'max:7'],
            'monthday'  => ['nullable', 'integer', 'min:1', 'max:28'],
        ]);

        Setting::set(self::FREQ_KEY, $data['frequency']);
        Setting::set(self::TIME_KEY, $data['time'] ?? '02:00');

        if ($data['frequency'] === 'weekly' && isset($data['weekday'])) {
            Setting::set(self::WEEKDAY_KEY, (string) $data['weekday']);
        }
        if ($data['frequency'] === 'monthly' && isset($data['monthday'])) {
            Setting::set(self::MONTHDAY_KEY, (string) $data['monthday']);
        }

        return redirect()->route('admin.backups.index')->with('status', 'schedule-saved');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function findSqlDump(string $dir): ?string
    {
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->isFile()) {
                $name = $file->getFilename();
                if (str_ends_with($name, '.sql') || str_ends_with($name, '.sql.gz')) {
                    return $file->getPathname();
                }
            }
        }
        return null;
    }

    private function gunzip(string $gzPath): string
    {
        $outPath = substr($gzPath, 0, -3);
        $gz = gzopen($gzPath, 'rb');
        $fp = fopen($outPath, 'wb');
        while (! gzeof($gz)) {
            fwrite($fp, gzread($gz, 65536));
        }
        gzclose($gz);
        fclose($fp);
        return $outPath;
    }

    private function restoreSqlDump(string $sqlFile): void
    {
        $sql = file_get_contents($sqlFile);

        if ($sql === false || trim($sql) === '') {
            throw new \RuntimeException('The database dump is empty or cannot be read.');
        }

        DB::connection()->getPdo()->exec($sql);
    }

    private function clearTransientTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach (self::TRANSIENT_TABLES as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }

            cache()->flush();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function rmdirRecursive(string $dir): void
    {
        if (! is_dir($dir)) return;
        foreach (array_diff(scandir($dir), ['.', '..']) as $entry) {
            $path = $dir . DIRECTORY_SEPARATOR . $entry;
            is_dir($path) ? $this->rmdirRecursive($path) : unlink($path);
        }
        rmdir($dir);
    }
}
