<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BackupController extends Controller
{
    public function index(): View
    {
        $backupName = config('backup.backup.name', config('app.name'));
        $disk       = config('backup.backup.destination.disks', ['local'])[0] ?? 'local';
        $path       = $backupName;

        $backups = [];

        if (Storage::disk($disk)->exists($path)) {
            $files = Storage::disk($disk)->files($path);
            foreach ($files as $file) {
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
        }

        return view('admin.backups.index', [
            'backups' => $backups,
            'disk'    => $disk,
        ]);
    }

    public function run(Request $request)
    {
        try {
            Artisan::call('backup:run', ['--disable-notifications' => true]);
            $output = Artisan::output();
            return back()->with('status', 'backup-success')->with('backup_output', $output);
        } catch (\Throwable $e) {
            return back()->with('status', 'backup-failed')->with('backup_error', $e->getMessage());
        }
    }

    public function download(Request $request)
    {
        $disk = $request->query('disk', 'local');
        $path = $request->query('path');

        if (! $path || ! Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        return Storage::disk($disk)->download($path, basename($path));
    }

    public function destroy(Request $request)
    {
        $disk = $request->query('disk', 'local');
        $path = $request->query('path');

        if (! $path || ! Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        Storage::disk($disk)->delete($path);

        return back()->with('status', 'backup-deleted');
    }
}
