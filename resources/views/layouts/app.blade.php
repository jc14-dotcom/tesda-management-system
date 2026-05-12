<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-grayTheme-light text-grayTheme-dark">
        <div class="app-shell" x-data="sidebarLayout()">
            @include('layouts.navigation')
            @include('layouts.sidebar')

            <!-- Page Heading -->
            <main
                class="transition-all duration-200"
                :class="desktopCollapsed ? 'sm:ml-16' : 'sm:ml-64'"
            >
                {{ $slot }}
            </main>
        </div>
        <!-- Notifications (toasts + error modal) -->
        <div x-data="notifications()" @show-toast.window="addToast($event.detail)" @show-error-modal.window="openErrorModal($event.detail)" x-cloak>
            <!-- Option C toast container -->
            <div id="toast-container-c" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none">
                <template x-for="t in toasts" :key="t.id">
                    <div x-bind:class="`toast-c ${t.type} pointer-events-auto relative overflow-hidden rounded-lg p-4 flex items-start gap-4 transition transform`" x-show="!t.removing" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-6" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 translate-x-4">
                        <div class="flex-shrink-0 text-white opacity-90">
                            <svg x-show="t.type === 'success'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="t.type === 'error'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <svg x-show="t.type === 'info'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/></svg>
                        </div>

                        <div class="flex-1 min-w-0 text-white">
                            <div class="font-semibold text-sm" x-text="t.title"></div>
                            <div class="text-xs opacity-90" x-text="t.message"></div>
                        </div>

                        <button type="button" class="text-white opacity-80 hover:opacity-100" @click="removeToast(t.id)">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <div class="absolute left-0 bottom-0 h-1 bg-white/40" :style="`width: ${t.progress}%`"></div>
                    </div>
                </template>
            </div>

            <!-- Error modal -->
            <div x-show="modal.open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/40" @click="closeModal()"></div>
                <div class="relative max-w-2xl w-full bg-white rounded-lg shadow-lg p-6 z-10">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900" x-text="modal.title"></h3>
                            <p class="mt-2 text-sm text-gray-600" x-text="modal.message"></p>
                        </div>
                        <button class="text-gray-500 hover:text-gray-800" @click="closeModal()">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <template x-if="modal.fieldErrors && Object.keys(modal.fieldErrors).length">
                        <div class="mt-4 space-y-3">
                            <div class="text-sm font-semibold text-gray-700">Validation Issues</div>
                            <ul class="text-sm list-disc list-inside text-gray-600">
                                <template x-for="(msgs, field) in modal.fieldErrors" :key="field">
                                    <li x-text="msgs.join('; ')" class="break-words"></li>
                                </template>
                            </ul>
                        </div>
                    </template>

                    <div class="mt-6 text-right">
                        <button class="px-4 py-2 rounded bg-gray-100 text-gray-800 hover:bg-gray-200" @click="closeModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
