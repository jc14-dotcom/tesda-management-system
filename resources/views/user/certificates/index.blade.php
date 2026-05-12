<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="My Certificates"
                subtitle="Add and manage your trainer and assessor certificates."
                eyebrow="Account"
            />

            <section class="p-4 sm:p-8 surface">
                <div class="mt-6">
                    @include('user.certificates.partials.certificates-form')
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
