<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Admin Dashboard"
                subtitle="Track users, certificates, and system activity at a glance."
                eyebrow="Administration"
            />

            @php
                $useSampleData = true;

                $statCards = [
                    [
                        'label' => 'Total Users',
                        'value' => $useSampleData ? 128 : $usersCount,
                        'note' => '8 new this month',
                        'tone' => 'bg-primary-soft text-primary',
                        'icon' => 'M16 19h4a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-2m-2.236-4a3 3 0 1 0 0-4M3 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
                    ],
                    [
                        'label' => 'Active Users',
                        'value' => $useSampleData ? 96 : null,
                        'note' => '75% active this week',
                        'tone' => 'bg-success-soft text-success',
                        'icon' => 'M9 12l2 2 4-4m5 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                    ],
                    [
                        'label' => 'Certificates Tracked',
                        'value' => $useSampleData ? 312 : $certificatesCount,
                        'note' => '14 expiring soon',
                        'tone' => 'bg-primary-soft text-primary',
                        'icon' => 'M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm5 4h6m-6 4h6',
                    ],
                    [
                        'label' => 'Documents Uploaded',
                        'value' => $useSampleData ? 540 : null,
                        'note' => '26 pending review',
                        'tone' => 'bg-info-soft text-info',
                        'icon' => 'M5 4h14a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Zm4 4h6m-6 4h6',
                    ],
                    [
                        'label' => 'Expiring Soon',
                        'value' => $useSampleData ? 14 : $expiringSoonCount,
                        'note' => 'Next 30 days',
                        'tone' => 'bg-warning-soft text-warning',
                        'icon' => 'M12 8v4l3 3m7-3A10 10 0 1 1 2 12a10 10 0 0 1 20 0Z',
                    ],
                    [
                        'label' => 'Expired',
                        'value' => $useSampleData ? 6 : $expiredCount,
                        'note' => 'Needs follow-up',
                        'tone' => 'bg-danger-soft text-danger',
                        'icon' => 'M12 9v4m0 4h.01m8.938-2A10 10 0 1 1 3.062 8a10 10 0 0 1 17.876 7Z',
                    ],
                ];

                $expiringCertificates = [
                    [
                        'user' => 'Maria Santos',
                        'certificate' => 'Bookkeeping NC II',
                        'expires' => '2026-06-18',
                        'status' => 'Expiring',
                        'tone' => 'bg-warning-soft text-warning',
                    ],
                    [
                        'user' => 'Ramon Cruz',
                        'certificate' => 'Cookery NC III',
                        'expires' => '2026-06-24',
                        'status' => 'Expiring',
                        'tone' => 'bg-warning-soft text-warning',
                    ],
                    [
                        'user' => 'Aira Villanueva',
                        'certificate' => 'NTTC Level I',
                        'expires' => '2026-07-02',
                        'status' => 'Renew',
                        'tone' => 'bg-accent-soft text-accent-hover',
                    ],
                ];

                $recentActivity = [
                    [
                        'title' => 'User profile updated',
                        'meta' => 'Maria Santos · Profile details',
                        'time' => '10 mins ago',
                    ],
                    [
                        'title' => 'Certificate uploaded',
                        'meta' => 'Ramon Cruz · Cookery NC III',
                        'time' => '1 hour ago',
                    ],
                    [
                        'title' => 'Admin reset password',
                        'meta' => 'Admin · John dela Cruz',
                        'time' => '3 hours ago',
                    ],
                ];

                $recentUsers = [
                    [
                        'name' => 'John dela Cruz',
                        'email' => 'john.delacruz@example.com',
                        'role' => 'User',
                        'joined' => 'May 03, 2026',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Angelica Reyes',
                        'email' => 'angelica.reyes@example.com',
                        'role' => 'User',
                        'joined' => 'May 06, 2026',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Eric Mendoza',
                        'email' => 'eric.mendoza@example.com',
                        'role' => 'Admin',
                        'joined' => 'May 08, 2026',
                        'status' => 'Active',
                    ],
                ];

                $recentUploads = [
                    [
                        'file' => 'Bookkeeping_NCII.pdf',
                        'user' => 'Maria Santos',
                        'type' => 'Certificate',
                        'time' => '2 days ago',
                    ],
                    [
                        'file' => 'Employment_Letter.pdf',
                        'user' => 'Angelica Reyes',
                        'type' => 'Document',
                        'time' => '4 days ago',
                    ],
                    [
                        'file' => 'Cookery_NCIII.jpg',
                        'user' => 'Ramon Cruz',
                        'type' => 'Certificate',
                        'time' => '1 week ago',
                    ],
                ];
            @endphp

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($statCards as $card)
                    <div class="surface p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm text-grayTheme-medium">{{ $card['label'] }}</p>
                                <p class="mt-2 text-3xl font-semibold text-grayTheme-dark">{{ $card['value'] ?? '—' }}</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $card['tone'] }}">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                    <path d="{{ $card['icon'] }}" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-grayTheme-medium">{{ $card['note'] }}</p>
                    </div>
                @endforeach
            </section>

            <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                <div class="surface p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-grayTheme-dark">Expiring Certificates</h3>
                            <p class="text-sm text-grayTheme-medium">Focus on renewals over the next 60 days.</p>
                        </div>
                        <span class="rounded-full bg-warning-soft px-3 py-1 text-xs font-semibold text-warning">Next 60 days</span>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-grayTheme-medium">
                                <tr>
                                    <th class="py-2">User</th>
                                    <th class="py-2">Certificate</th>
                                    <th class="py-2">Expires</th>
                                    <th class="py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($expiringCertificates as $item)
                                    <tr>
                                        <td class="py-2 font-medium text-grayTheme-dark">{{ $item['user'] }}</td>
                                        <td class="py-2">{{ $item['certificate'] }}</td>
                                        <td class="py-2">{{ $item['expires'] }}</td>
                                        <td class="py-2">
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item['tone'] }}">
                                                {{ $item['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="surface p-6">
                    <h3 class="text-lg font-semibold text-grayTheme-dark">Recent Activity</h3>
                    <p class="text-sm text-grayTheme-medium">Latest updates from admins and users.</p>

                    <div class="mt-4 space-y-3">
                        @foreach ($recentActivity as $activity)
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-grayTheme-border bg-white px-4 py-3">
                                <div>
                                    <p class="text-sm font-semibold text-grayTheme-dark">{{ $activity['title'] }}</p>
                                    <p class="text-xs text-grayTheme-medium">{{ $activity['meta'] }}</p>
                                </div>
                                <span class="text-xs font-semibold text-grayTheme-medium">{{ $activity['time'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div class="surface p-6">
                    <h3 class="text-lg font-semibold text-grayTheme-dark">Recent Users</h3>
                    <p class="text-sm text-grayTheme-medium">New accounts created recently.</p>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-grayTheme-medium">
                                <tr>
                                    <th class="py-2">Name</th>
                                    <th class="py-2">Email</th>
                                    <th class="py-2">Role</th>
                                    <th class="py-2">Joined</th>
                                    <th class="py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($recentUsers as $user)
                                    <tr>
                                        <td class="py-2 font-medium text-grayTheme-dark">{{ $user['name'] }}</td>
                                        <td class="py-2">{{ $user['email'] }}</td>
                                        <td class="py-2">{{ $user['role'] }}</td>
                                        <td class="py-2">{{ $user['joined'] }}</td>
                                        <td class="py-2">
                                            <span class="rounded-full bg-success-soft px-3 py-1 text-xs font-semibold text-success">{{ $user['status'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="surface p-6">
                    <h3 class="text-lg font-semibold text-grayTheme-dark">Recent Uploads</h3>
                    <p class="text-sm text-grayTheme-medium">Latest documents submitted by users.</p>

                    <div class="mt-4 space-y-3">
                        @foreach ($recentUploads as $upload)
                            <div class="flex items-center justify-between rounded-2xl border border-grayTheme-border bg-white px-4 py-3">
                                <div>
                                    <p class="text-sm font-semibold text-grayTheme-dark">{{ $upload['file'] }}</p>
                                    <p class="text-xs text-grayTheme-medium">{{ $upload['user'] }} · {{ $upload['type'] }}</p>
                                </div>
                                <span class="text-xs font-semibold text-grayTheme-medium">{{ $upload['time'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
