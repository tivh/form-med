<?php

namespace App\Http\Controllers;

use App\Models\ComplianceDocument;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ComplianceDocumentController extends Controller
{
    public function index(): View
    {
        $documents = ComplianceDocument::active()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->groupBy('category');

        return view('compliance.index', ['documentsByCategory' => $documents]);
    }

    public function download(ComplianceDocument $complianceDocument): Response
    {
        abort_unless($complianceDocument->is_active, 404);

        abort_unless(
            Storage::disk('private_uploads')->exists($complianceDocument->file_path),
            404,
            'Arquivo não encontrado.'
        );

        return response()->download(
            Storage::disk('private_uploads')->path($complianceDocument->file_path),
            $complianceDocument->file_original_name
        );
    }
}
