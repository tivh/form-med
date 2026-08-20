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
            'code_of_conduct_pf' => Setting::get('code_of_conduct_pf', ''),
            'code_of_conduct_pj' => Setting::get('code_of_conduct_pj', ''),
            'integrity_policy_pf' => Setting::get('integrity_policy_pf', ''),
            'integrity_policy_pj' => Setting::get('integrity_policy_pj', ''),
            'data_protection_pf' => Setting::get('data_protection_pf', ''),
            'data_protection_pj' => Setting::get('data_protection_pj', ''),
            'code_of_conduct_version_pf' => Setting::get('code_of_conduct_version_pf', 'v1.0'),
            'code_of_conduct_version_pj' => Setting::get('code_of_conduct_version_pj', 'v1.0'),
            'integrity_policy_version_pf' => Setting::get('integrity_policy_version_pf', 'v1.0'),
            'integrity_policy_version_pj' => Setting::get('integrity_policy_version_pj', 'v1.0'),
            'data_protection_version_pf' => Setting::get('data_protection_version_pf', 'v1.0'),
            'data_protection_version_pj' => Setting::get('data_protection_version_pj', 'v1.0'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'terms_pf' => ['nullable', 'string', 'max:10000'],
            'terms_pj' => ['nullable', 'string', 'max:10000'],
            'code_of_conduct_pf' => ['nullable', 'string', 'max:10000'],
            'code_of_conduct_pj' => ['nullable', 'string', 'max:10000'],
            'integrity_policy_pf' => ['nullable', 'string', 'max:10000'],
            'integrity_policy_pj' => ['nullable', 'string', 'max:10000'],
            'data_protection_pf' => ['nullable', 'string', 'max:10000'],
            'data_protection_pj' => ['nullable', 'string', 'max:10000'],
            'code_of_conduct_version_pf' => ['nullable', 'string', 'max:50'],
            'code_of_conduct_version_pj' => ['nullable', 'string', 'max:50'],
            'integrity_policy_version_pf' => ['nullable', 'string', 'max:50'],
            'integrity_policy_version_pj' => ['nullable', 'string', 'max:50'],
            'data_protection_version_pf' => ['nullable', 'string', 'max:50'],
            'data_protection_version_pj' => ['nullable', 'string', 'max:50'],
        ]);

        Setting::set('terms_pf', $request->input('terms_pf', ''));
        Setting::set('terms_pj', $request->input('terms_pj', ''));
        Setting::set('code_of_conduct_pf', $request->input('code_of_conduct_pf', ''));
        Setting::set('code_of_conduct_pj', $request->input('code_of_conduct_pj', ''));
        Setting::set('integrity_policy_pf', $request->input('integrity_policy_pf', ''));
        Setting::set('integrity_policy_pj', $request->input('integrity_policy_pj', ''));
        Setting::set('data_protection_pf', $request->input('data_protection_pf', ''));
        Setting::set('data_protection_pj', $request->input('data_protection_pj', ''));
        Setting::set('code_of_conduct_version_pf', $request->input('code_of_conduct_version_pf', 'v1.0'));
        Setting::set('code_of_conduct_version_pj', $request->input('code_of_conduct_version_pj', 'v1.0'));
        Setting::set('integrity_policy_version_pf', $request->input('integrity_policy_version_pf', 'v1.0'));
        Setting::set('integrity_policy_version_pj', $request->input('integrity_policy_version_pj', 'v1.0'));
        Setting::set('data_protection_version_pf', $request->input('data_protection_version_pf', 'v1.0'));
        Setting::set('data_protection_version_pj', $request->input('data_protection_version_pj', 'v1.0'));

        return back()->with('success', 'Configurações salvas com sucesso.');
    }
}
