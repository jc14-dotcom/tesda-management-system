<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Dashboard"
                subtitle="Overview of your account activity."
                eyebrow="Account"
            />

            <div class="surface overflow-hidden">
                <div class="p-6 text-slate-900">
                    {{ __("You're logged in!") }}

                    @if (auth()->user()?->hasRole('admin'))
                        <div class="mt-4">
                            <a class="text-primary hover:text-blue-800" href="{{ route('admin.dashboard') }}">
                                Go to Admin Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
