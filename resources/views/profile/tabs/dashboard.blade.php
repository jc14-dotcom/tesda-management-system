<div>
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-800">Dashboard</h2>
    </div>
    <div class="mt-4">
        <p class="text-sm text-slate-600">Welcome back, {{ $user->name }}. Summary of your account:</p>
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 surface">
                <p class="text-sm text-slate-500">Certificates</p>
                <p class="text-2xl font-bold text-slate-800">{{ $certificatesCount }}</p>
            </div>
            <div class="p-4 surface">
                <p class="text-sm text-slate-500">Documents</p>
                <p class="text-2xl font-bold text-slate-800">{{ $documentsCount }}</p>
            </div>
            <div class="p-4 surface">
                <p class="text-sm text-slate-500">Notifications</p>
                <p class="text-2xl font-bold text-slate-800">0</p>
            </div>
        </div>
    </div>
</div>
