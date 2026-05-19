<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DpaController extends Controller
{
    public function show(Request $request): Response|RedirectResponse
    {
        $user = auth()->user();

        if ($user && $user->dpa_agreed_at !== null) {
            $redirectRoute = $user->hasRole('admin') ? 'admin.dashboard' : 'dashboard';
            return redirect()->route($redirectRoute);
        }

        return response()
            ->view('dpa.accept')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function accept(Request $request): RedirectResponse
    {
        $request->validate([
            'agree' => ['required', 'accepted'],
        ]);

        $user = auth()->user();

        $user->forceFill(['dpa_agreed_at' => now()])->save();

        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->event('dpa_accepted')
            ->log('User accepted the Data Privacy Act and Terms of Use');

        $redirectRoute = $user->hasRole('admin') ? 'admin.dashboard' : 'dashboard';

        return redirect()->route($redirectRoute);
    }
}
