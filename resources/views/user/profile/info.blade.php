<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="My Profile"
                subtitle="Update your personal and employment details."
                eyebrow="Account"
            />

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
