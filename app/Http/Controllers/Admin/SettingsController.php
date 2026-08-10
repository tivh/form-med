<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'terms_pf' => Setting::get('terms_pf', ''),
            'terms_pj' => Setting::get('terms_pj', ''),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'terms_pf' => ['nullable', 'string', 'max:10000'],
            'terms_pj' => ['nullable', 'string', 'max:10000'],
        ]);

        Setting::set('terms_pf', $request->input('terms_pf', ''));
        Setting::set('terms_pj', $request->input('terms_pj', ''));

        return back()->with('success', 'Configurações salvas com sucesso.');
    }
}
