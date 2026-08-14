<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="My Documents"
                subtitle="Upload supporting documents and certificate files."
                eyebrow="Account"
            />

            <section class="px-4 pb-4 pt-3 sm:px-8 sm:pb-8 sm:pt-5 surface">
                <div>
                    @include('user.documents.partials.documents-form')
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
