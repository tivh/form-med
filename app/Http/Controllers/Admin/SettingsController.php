<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalDocument;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $documentGroups = [
            'code_of_conduct' => ['label' => 'Código de conduta'],
            'integrity_policy' => ['label' => 'Política de integridade'],
            'data_protection' => ['label' => 'Termo de proteção de dados pessoais - LGPD'],
        ];

        $data = [];
        foreach (['pf', 'pj'] as $personType) {
            foreach (array_keys($documentGroups) as $documentKey) {
                $fallbackText = Setting::get("{$documentKey}_{$personType}", '');
                $fallbackVersion = Setting::get("{$documentKey}_version_{$personType}", 'v1.0');
                $document = LegalDocument::getDocument($documentKey, $personType, $documentGroups[$documentKey]['label'], $fallbackText, $fallbackVersion);

                $data["{$documentKey}_{$personType}"] = $document['text'];
                $data["{$documentKey}_version_{$personType}"] = $document['version'];
                $data["{$documentKey}_updated_{$personType}"] = $document['updated_at'];
            }
        }

        $viewData = [
            'terms_pf' => Setting::get('terms_pf', ''),
            'terms_pj' => Setting::get('terms_pj', ''),
        ];

        return view('admin.settings.index', array_merge($viewData, $data));
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

        $documentGroups = [
            'code_of_conduct' => 'Código de conduta',
            'integrity_policy' => 'Política de integridade',
            'data_protection' => 'Termo de proteção de dados pessoais - LGPD',
        ];

        foreach (['pf', 'pj'] as $personType) {
            foreach ($documentGroups as $documentKey => $title) {
                $text = $request->input("{$documentKey}_{$personType}", '');
                $version = $request->input("{$documentKey}_version_{$personType}", 'v1.0');

                LegalDocument::saveDocument($documentKey, $personType, $title, $text, $version);
                Setting::set("{$documentKey}_{$personType}", $text);
                Setting::set("{$documentKey}_version_{$personType}", $version);
            }
        }

        return back()->with('success', 'Configurações salvas com sucesso.');
    }
}
