<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="My Documents"
                subtitle="Upload supporting documents and certificate files."
                eyebrow="Account"
            />

            <section class="p-4 sm:p-8 surface">
                <div class="mt-6">
                    @include('user.documents.partials.documents-form')
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
