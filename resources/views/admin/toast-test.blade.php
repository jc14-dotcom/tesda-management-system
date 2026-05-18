<x-app-layout>
    <x-page-header title="Toast Notification Test">
        <x-slot:back>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-active transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Dashboard
            </a>
        </x-slot:back>
    </x-page-header>

    <div class="page-container py-8">
        <div class="max-w-3xl mx-auto">
            <div class="surface p-8">
                <h2 class="text-xl font-bold text-grayTheme-dark mb-6">Test Toast Notifications (Option C - Maroon Gradient Theme)</h2>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-grayTheme-dark mb-2">Success Toast</h3>
                        <button 
                            type="button" 
                            class="btn-primary"
                            onclick="window.dispatchEvent(new CustomEvent('show-toast', {detail: {type: 'success', title: 'Success!', message: 'Admin account has been disabled successfully.'}}))">
                            Show Success Toast
                        </button>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-grayTheme-dark mb-2">Error Toast</h3>
                        <button 
                            type="button" 
                            class="btn-danger"
                            onclick="window.dispatchEvent(new CustomEvent('show-toast', {detail: {type: 'error', title: 'Error!', message: 'Something went wrong. Please try again.'}}))">
                            Show Error Toast
                        </button>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-grayTheme-dark mb-2">Warning Toast</h3>
                        <button 
                            type="button" 
                            class="btn-secondary"
                            onclick="window.dispatchEvent(new CustomEvent('show-toast', {detail: {type: 'warning', title: 'Warning!', message: 'This action cannot be undone.'}}))">
                            Show Warning Toast
                        </button>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-grayTheme-dark mb-2">Info Toast</h3>
                        <button 
                            type="button" 
                            class="btn-secondary"
                            onclick="window.dispatchEvent(new CustomEvent('show-toast', {detail: {type: 'info', title: 'Info', message: 'Election has been scheduled for today.'}}))">
                            Show Info Toast
                        </button>
                    </div>

                    <div class="pt-4 mt-6 border-t border-grayTheme-border">
                        <h3 class="text-sm font-semibold text-grayTheme-dark mb-2">Multiple Toasts</h3>
                        <button 
                            type="button" 
                            class="btn-primary"
                            onclick="
                                window.dispatchEvent(new CustomEvent('show-toast', {detail: {type: 'success', title: 'Success!', message: 'First notification'}}));
                                setTimeout(() => window.dispatchEvent(new CustomEvent('show-toast', {detail: {type: 'info', title: 'Info', message: 'Second notification'}})), 500);
                                setTimeout(() => window.dispatchEvent(new CustomEvent('show-toast', {detail: {type: 'warning', title: 'Warning!', message: 'Third notification'}})), 1000);
                            ">
                            Show Multiple Toasts
                        </button>
                    </div>
                </div>

                <div class="mt-8 p-4 bg-primary-soft rounded-lg">
                    <h3 class="text-sm font-bold text-primary mb-2">Design Features (Option C):</h3>
                    <ul class="text-sm text-grayTheme-dark space-y-1 list-disc list-inside">
                        <li>Gradient backgrounds matching system theme (maroon for errors)</li>
                        <li>White text on colored backgrounds</li>
                        <li>Font Awesome-style solid icons (check circle, exclamation, warning triangle, info circle)</li>
                        <li>Progress bar at bottom with transparent white color</li>
                        <li>Smooth slide-in animation from right</li>
                        <li>Auto-dismiss after 4.5 seconds</li>
                        <li>Top-right corner positioning</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
