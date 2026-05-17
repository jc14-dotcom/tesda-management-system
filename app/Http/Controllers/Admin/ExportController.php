<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    public function certificates(Request $request): Response
    {
        $status = $request->query('status', 'all');
        $type   = $request->query('type', 'all');

        $rows = Certificate::with('user:id,name,email')
            ->select([
                'id', 'user_id', 'certificate_name', 'certificate_type',
                'qualification_title', 'certificate_number', 'issued_by',
                'issue_date', 'expiration_date', 'status', 'verification_status',
                'verified_at',
            ])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($type !== 'all', fn ($q) => $q->where('certificate_type', $type))
            ->orderBy('user_id')
            ->get();

        $headers = [
            'User Name', 'User Email', 'Certificate Name', 'Type',
            'Qualification', 'Certificate No.', 'Issued By',
            'Issue Date', 'Expiry Date', 'Status', 'Verification',
        ];

        $csv = implode(',', array_map([$this, 'escCsv'], $headers)) . "\n";

        foreach ($rows as $cert) {
            $csv .= implode(',', array_map([$this, 'escCsv'], [
                $cert->user->name ?? '',
                $cert->user->email ?? '',
                $cert->certificate_name,
                Certificate::TYPE_LABELS[$cert->certificate_type] ?? $cert->certificate_type,
                $cert->qualification_title ?? '',
                $cert->certificate_number ?? '',
                $cert->issued_by ?? '',
                $cert->issue_date?->format('Y-m-d') ?? '',
                $cert->expiration_date?->format('Y-m-d') ?? '',
                ucfirst($cert->status),
                ucfirst($cert->verification_status ?? 'pending'),
            ])) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="certificates-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function users(Request $request): Response
    {
        $rows = User::with(['profile', 'roles:id,name'])
            ->withCount('certificates')
            ->orderBy('name')
            ->get();

        $headers = [
            'Name', 'Email', 'Role', 'Status',
            'Position', 'Region', 'Branch',
            'TESDA Registry No.', 'Certificates', 'Registered',
        ];

        $csv = implode(',', array_map([$this, 'escCsv'], $headers)) . "\n";

        foreach ($rows as $user) {
            $csv .= implode(',', array_map([$this, 'escCsv'], [
                $user->name,
                $user->email,
                ucfirst($user->roles->first()?->name ?? 'user'),
                ucfirst($user->profile?->status ?? 'active'),
                $user->profile?->position_title ?? '',
                $user->profile?->region ?? '',
                $user->profile?->branch ?? '',
                $user->profile?->tesda_registry_number ?? '',
                $user->certificates_count,
                $user->created_at->format('Y-m-d'),
            ])) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    private function escCsv(string $value): string
    {
        $value = str_replace('"', '""', $value);
        return '"' . $value . '"';
    }
}
