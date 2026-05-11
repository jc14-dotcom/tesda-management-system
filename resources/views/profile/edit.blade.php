<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            @php
                $activeTab = request('tab', 'dashboard');
                $headers = [
                    'dashboard' => [
                        'title' => 'Dashboard',
                        'subtitle' => 'Quick snapshot of your certificates, documents, and alerts.',
                        'eyebrow' => 'Account',
                    ],
                    'profile' => [
                        'title' => 'My Profile',
                        'subtitle' => 'Update your personal and employment details.',
                        'eyebrow' => 'Account',
                    ],
                    'certificates' => [
                        'title' => 'My Certificates',
                        'subtitle' => 'Add and manage your trainer and assessor certificates.',
                        'eyebrow' => 'Account',
                    ],
                    'documents' => [
                        'title' => 'My Documents',
                        'subtitle' => 'Upload supporting documents and certificate files.',
                        'eyebrow' => 'Account',
                    ],
                    'notifications' => [
                        'title' => 'Notifications',
                        'subtitle' => 'Stay up to date with account alerts.',
                        'eyebrow' => 'Account',
                    ],
                    'settings' => [
                        'title' => 'Account Settings',
                        'subtitle' => 'Manage your password and security preferences.',
                        'eyebrow' => 'Account',
                    ],
                ];
                $header = $headers[$activeTab] ?? $headers['dashboard'];
            @endphp

            <x-page-header
                :title="$header['title']"
                :subtitle="$header['subtitle']"
                :eyebrow="$header['eyebrow']"
            />

            @switch($activeTab)
                @case('profile')
                    <section id="update-profile-information" class="p-4 sm:p-8 surface">
                        <div class="mt-6 space-y-6">
                            <div class="max-w-xl">
                                @include('profile.partials.update-profile-information-form')
                            </div>

                            <div id="update-profile-details" class="max-w-5xl">
                                @include('profile.partials.update-profile-details-form')
                            </div>
                        </div>
                    </section>
                    @break

                @case('certificates')
                    <section class="p-4 sm:p-8 surface">
                        <div class="mt-6">
                            @include('profile.partials.certificates-form')
                        </div>
                    </section>
                    @break

                @case('documents')
                    <section class="p-4 sm:p-8 surface">
                        <div class="mt-6">
                            @include('profile.partials.documents-form')
                        </div>
                    </section>
                    @break

                @case('notifications')
                    <section class="p-4 sm:p-8 surface">
                        <div class="mt-4 text-sm text-slate-600">You have no notifications yet.</div>
                    </section>
                    @break

                @case('settings')
                    <section class="p-4 sm:p-8 surface">
                        <div class="mt-6 space-y-6">
                            <div id="update-password" class="max-w-xl">
                                @include('profile.partials.update-password-form')
                            </div>

                            <div id="delete-account" class="max-w-xl">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </section>
                    @break

                @default
                    <section class="p-4 sm:p-8 surface">
                        <div class="mt-4">
                            <p class="text-sm text-slate-600">Welcome back, {{ auth()->user()->name }}. Summary of your account:</p>
                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="p-4 surface">
                                    <p class="text-sm text-slate-500">Certificates</p>
                                    <p class="text-2xl font-bold text-slate-800">{{ $certificatesCount ?? 0 }}</p>
                                </div>
                                <div class="p-4 surface">
                                    <p class="text-sm text-slate-500">Documents</p>
                                    <p class="text-2xl font-bold text-slate-800">{{ $documentsCount ?? 0 }}</p>
                                </div>
                                <div class="p-4 surface">
                                    <p class="text-sm text-slate-500">Notifications</p>
                                    <p class="text-2xl font-bold text-slate-800">0</p>
                                </div>
                            </div>
                        </div>
                    </section>
            @endswitch
        </div>
    </div>
</x-app-layout>
