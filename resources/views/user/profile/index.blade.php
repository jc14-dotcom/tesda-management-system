<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="My Profile"
                subtitle="Update your personal and employment details."
                eyebrow="Account"
            />

            <!-- Section quick-nav -->
            <div class="flex flex-wrap items-center gap-2 rounded-card border border-grayTheme-border bg-white px-4 py-3 shadow-card">
                <span class="mr-1 text-xs font-semibold uppercase tracking-widest text-grayTheme-medium">Jump to:</span>
                <a href="#update-profile-information" class="inline-flex items-center gap-1.5 rounded-button border border-grayTheme-border bg-white px-3 py-1.5 text-xs font-semibold text-grayTheme-medium transition hover:border-primary hover:bg-primary-soft hover:text-primary">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Profile Info
                </a>
                <a href="#update-profile-details" class="inline-flex items-center gap-1.5 rounded-button border border-grayTheme-border bg-white px-3 py-1.5 text-xs font-semibold text-grayTheme-medium transition hover:border-primary hover:bg-primary-soft hover:text-primary">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    Personal &amp; Employment
                </a>
            </div>

            <div class="space-y-6">
                <section id="update-profile-information" class="p-4 sm:p-8 surface">
                    <div class="max-w-5xl">
                        @include('user.profile.partials.update-profile-information-form')
                    </div>
                </section>

                <div id="update-profile-details">
                    @include('user.profile.partials.update-profile-details-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
