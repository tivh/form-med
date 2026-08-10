<?php

namespace App\Http\Controllers;

use App\Exports\TaxRegimeSubmissionsExport;
use App\Http\Controllers\Controller;
use App\Models\TaxRegimeSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;

class TaxRegimeSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->canAccess('regime-tributario'), 403);

        $query = TaxRegimeSubmission::query()->latest();

        if ($request->filled('razao_social')) {
            $query->where('razao_social', 'like', '%'.$request->input('razao_social').'%');
        }
        if ($request->filled('cnpj')) {
            $query->where('cnpj', 'like', '%'.$request->input('cnpj').'%');
        }

        $submissions = $query->paginate(20)->withQueryString();

        return view('admin.tax-regime.index', [
            'submissions' => $submissions,
            'filters' => $request->only(['razao_social', 'cnpj']),
        ]);
    }

    public function show(Request $request, TaxRegimeSubmission $taxRegimeSubmission): View
    {
        abort_unless($request->user()->canAccess('regime-tributario'), 403);

        return view('admin.tax-regime.show', ['submission' => $taxRegimeSubmission]);
    }

    public function toggleVerified(Request $request, TaxRegimeSubmission $taxRegimeSubmission): RedirectResponse
    {
        abort_unless($request->user()->canAccess('regime-tributario'), 403);

        $taxRegimeSubmission->update([
            'verified' => (bool) $request->boolean('verified'),
        ]);

        return back()->with('status', 'Status de verificação atualizado com sucesso.');
    }

    public function destroy(Request $request, TaxRegimeSubmission $taxRegimeSubmission): RedirectResponse
    {
        abort_unless($request->user()->canAccess('regime-tributario'), 403);

        $taxRegimeSubmission->delete();

        return redirect()->route('admin.tax-regime.index')->with('status', 'Registro excluído com sucesso.');
    }

    public function export(Request $request)
    {
        abort_unless($request->user()->canAccess('regime-tributario'), 403);

        $format = strtolower((string) $request->get('format')) === 'xlsx' ? Excel::XLSX : Excel::CSV;
        $fileName = 'regime_tributario.'.($format === Excel::XLSX ? 'xlsx' : 'csv');

        return ExcelFacade::download(
            new TaxRegimeSubmissionsExport($request->only(['razao_social', 'cnpj'])),
            $fileName,
            $format
        );
    }
}