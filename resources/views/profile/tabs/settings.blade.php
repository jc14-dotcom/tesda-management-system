<div>
    <h2 class="text-lg font-semibold text-slate-800">Account Settings</h2>
    <div class="mt-6 space-y-6">
        <div id="update-password" class="max-w-xl">
            @include('profile.partials.update-password-form')
        </div>

        <div id="delete-account" class="max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
