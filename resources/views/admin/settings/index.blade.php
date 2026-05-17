<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Settings"
                subtitle="System configuration and notification preferences."
                eyebrow="Administration"
            />

            <div class="max-w-2xl space-y-6">

                @if(session('status') === 'settings-saved')
                    <div class="mb-4 rounded-lg bg-success-soft px-4 py-3 text-sm font-semibold text-success">Settings saved.</div>
                @endif

                <form method="post" action="{{ route('admin.settings.update') }}" class="space-y-6">
                    @csrf

                    <div class="surface rounded-xl p-6 space-y-6">
                        <h2 class="text-base font-semibold text-grayTheme-dark">Certificate Notifications</h2>

                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                id="notifications_enabled"
                                name="notifications_enabled"
                                value="1"
                                class="h-4 w-4 rounded border-grayTheme-border text-primary focus:ring-primary"
                                @checked($notificationsEnabled ?? false)
                            />
                            <label for="notifications_enabled" class="text-sm font-medium text-grayTheme-dark">
                                Enable expiry notifications
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-grayTheme-dark mb-2">
                                Notify users this many days before expiry
                            </label>
                            <div class="flex flex-wrap gap-2" x-data="{ days: {{ json_encode($expiryNoticeDays ?? [30,14,7,3,1]) }} }">
                                <template x-for="(day, i) in days" :key="i">
                                    <div class="flex items-center gap-1">
                                        <input
                                            type="number"
                                            :name="'expiry_notice_days[]'"
                                            x-model.number="days[i]"
                                            class="form-input w-20 text-sm"
                                            min="1" max="365"
                                        />
                                        <button type="button" @click="days.splice(i,1)" class="text-danger text-sm hover:underline">✕</button>
                                    </div>
                                </template>
                                <button type="button" @click="days.push(1)" class="btn-secondary text-xs">+ Add Day</button>
                            </div>
                            @error('expiry_notice_days')
                                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
