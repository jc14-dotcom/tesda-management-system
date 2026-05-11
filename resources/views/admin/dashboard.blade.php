<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Admin Dashboard"
                subtitle="Summary of system activity and user records."
                eyebrow="Administration"
            />

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div class="surface p-6">
                    <div class="text-sm text-slate-500">Total Users</div>
                    <div class="mt-2 text-3xl font-semibold text-slate-900">{{ $usersCount }}</div>
                </div>
                <div class="surface p-6">
                    <div class="text-sm text-slate-500">Total Certificates</div>
                    <div class="mt-2 text-3xl font-semibold text-slate-900">{{ $certificatesCount }}</div>
                </div>
                <div class="surface p-6">
                    <div class="text-sm text-slate-500">Expiring Within 30 Days</div>
                    <div class="mt-2 text-3xl font-semibold text-amber-600">{{ $expiringSoonCount }}</div>
                </div>
                <div class="surface p-6">
                    <div class="text-sm text-slate-500">Expired</div>
                    <div class="mt-2 text-3xl font-semibold text-red-600">{{ $expiredCount }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
