<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Qualification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QualificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type'  => ['required', 'string', Rule::in(['trainer', 'assessor'])],
            'title' => ['required', 'string', 'max:255'],
        ]);

        $data['title'] = trim($data['title']);

        $exists = Qualification::where('type', $data['type'])
            ->whereRaw('LOWER(TRIM(title)) = ?', [strtolower($data['title'])])
            ->exists();

        if ($exists) {
            $errorKey = 'title_' . $data['type'];
            return back()
                ->withErrors([$errorKey => 'This qualification already exists.'])
                ->withInput();
        }

        Qualification::create($data);

        return back()->with('status', 'qualification-added');
    }

    public function destroy(Qualification $qualification): RedirectResponse
    {
        $qualification->delete();

        return back()->with('status', 'qualification-deleted');
    }
}
