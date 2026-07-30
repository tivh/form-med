<?php

namespace App\Http\Controllers;

use App\Models\TaxRegimeSubmission;
use App\Rules\Cnpj;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxRegimeFormController extends Controller
{
    public function show(): View
    {
        return view('tax-regime-form');
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'razao_social' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'max:50', new Cnpj],
            'regime_tributario' => ['required', 'string', 'in:Simples Nacional,Lucro Presumido,Lucro Real'],
            'lc_214_2025_compliant' => ['required', 'boolean'],
        ]);

        TaxRegimeSubmission::create($validated);

        return redirect()->route('tax-regime.success');
    }

    public function success(): View
    {
        return view('tax-regime-success');
    }
}