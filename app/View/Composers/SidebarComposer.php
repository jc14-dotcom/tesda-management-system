<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Provides the $isAdminUser variable to the sidebar layout.
 *
 * Using a view composer here serves two purposes:
 *  1. Removes all logic from the Blade template (views should not query auth/roles directly).
 *  2. Caches the role check per user so repeated navigation does not hit the role-
 *     permission tables on every request.
 */
class SidebarComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();

        if (! $user) {
            $view->with('isAdminUser', false);
            return;
        }

        $isAdmin = Cache::remember(
            "user:{$user->id}:is_admin",
            now()->addHour(),
            fn () => (bool) $user->hasRole('admin'),
        );

        $view->with('isAdminUser', $isAdmin);
    }
}
