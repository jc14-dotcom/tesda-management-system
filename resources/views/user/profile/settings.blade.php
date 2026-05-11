<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Account Settings"
                subtitle="Manage your password and security preferences."
                eyebrow="Account"
            />

            <section class="p-4 sm:p-8 surface">
                <div class="mt-6 space-y-6">
                    <div id="update-password" class="max-w-xl">
                        @include('user.profile.partials.update-password-form')
                    </div>

                    <div id="delete-account" class="max-w-xl">
                        @include('user.profile.partials.delete-user-form')
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
