<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Account Settings"
                subtitle="Manage your password and security preferences."
                eyebrow="Account"
            />

            {{-- Password Section --}}
            <section id="update-password" class="p-4 sm:p-8 surface">
                @include('user.settings.partials.update-password-form')
            </section>

            {{-- Danger Zone --}}
            <section id="delete-account" class="overflow-hidden rounded-card border border-danger/30 bg-white shadow-card">
                @include('user.settings.partials.delete-user-form')
            </section>
        </div>
    </div>
</x-app-layout>
