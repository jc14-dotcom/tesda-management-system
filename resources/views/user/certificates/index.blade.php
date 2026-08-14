<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="My Certificates"
                subtitle="Add and manage your trainer and assessor certificates."
                eyebrow="Account"
            />

            <section class="px-4 pb-4 pt-3 sm:px-8 sm:pb-8 sm:pt-5 surface">
                <div>
                    @include('user.certificates.partials.certificates-form')
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
