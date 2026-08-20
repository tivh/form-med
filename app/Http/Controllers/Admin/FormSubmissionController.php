<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FormSubmissionsExport;
use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;

class FormSubmissionController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->canAccess('form-med'), 403);

        $user = $request->user();
        $query = FormSubmission::query()->latest();
        $formCatalog = $this->formCatalog();

        $allowedClassifications = $user->allowedClassifications();
        if ($user->isSuperAdmin() === false && $allowedClassifications !== []) {
            $query->whereIn('classification', $allowedClassifications);
        }

        if ($request->filled('email')) {
            $email = trim((string) $request->input('email'));
            $query->where('email', 'like', '%'.$email.'%');
        }

        if ($request->filled('name')) {
            $name = trim((string) $request->input('name'));
            $query->where(function ($q) use ($name) {
                $q->where('nome', 'like', '%'.$name.'%')
                    ->orWhere('razao_social', 'like', '%'.$name.'%')
                    ->orWhere('nome_fantasia', 'like', '%'.$name.'%');
            });
        }

        if ($request->filled('registration_type')) {
            $type = $request->input('registration_type');
            if (in_array($type, ['pf', 'pj'], true)) {
                $query->where('registration_type', $type);
            }
        }

        if ($request->filled('form_type')) {
            $query->where('form_type', $request->input('form_type'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $submissions = $query->paginate(20)->withQueryString();

        return view('admin.submissions.index', [
            'submissions' => $submissions,
            'filters' => $request->only(['email', 'name', 'registration_type', 'from', 'to', 'form_type']),
            'formCatalog' => $formCatalog,
        ]);
    }

    public function show(Request $request, FormSubmission $submission): View
    {
        abort_unless($request->user()->canViewSubmission($submission), 403);

        return view('admin.submissions.show', [
            'submission' => $submission,
            'formCatalog' => $this->formCatalog(),
        ]);
    }

    public function toggleVerified(Request $request, FormSubmission $submission): RedirectResponse
    {
        $submission->update([
            'verified' => (bool) $request->boolean('verified'),
        ]);

        return back()->with('status', 'Status de verificação atualizado com sucesso.');
    }

    public function destroy(FormSubmission $submission): RedirectResponse
    {
        if (is_array($submission->documents)) {
            foreach ($submission->documents as $doc) {
                if (!empty($doc['path']) && Storage::disk('private_uploads')->exists($doc['path'])) {
                    Storage::disk('private_uploads')->delete($doc['path']);
                }
            }
        }

        if (is_array($submission->required_documents)) {
            foreach ($submission->required_documents as $doc) {
                if (!empty($doc['path']) && Storage::disk('private_uploads')->exists($doc['path'])) {
                    Storage::disk('private_uploads')->delete($doc['path']);
                }
            }
        }

        $submission->delete();

        return redirect()->route('admin.submissions.index')->with('status', 'Registro excluído com sucesso.');
    }

    public function download(FormSubmission $submission)
    {
        $requiredDocumentKey = request()->get('required_doc');
        if ($requiredDocumentKey) {
            $requiredDocuments = is_array($submission->required_documents) ? $submission->required_documents : [];
            $doc = $requiredDocuments[$requiredDocumentKey] ?? null;

            if (!$doc || empty($doc['path']) || !Storage::disk('private_uploads')->exists($doc['path'])) {
                abort(404);
            }

            return Storage::disk('private_uploads')->download($doc['path'], $doc['original_name'] ?? basename($doc['path']));
        }

        $documents = is_array($submission->documents) ? $submission->documents : [];
        $index = (int) request()->get('doc', 0);
        $doc = $documents[$index] ?? null;

        if (!$doc || empty($doc['path']) || !Storage::disk('private_uploads')->exists($doc['path'])) {
            abort(404);
        }

        $downloadName = $doc['original_name'] ?? basename($doc['path']);

        return Storage::disk('private_uploads')->download($doc['path'], $downloadName);
    }

    public function print(FormSubmission $submission)
    {
        $isPj = $submission->registration_type === 'pj';
        $documents = [
            [
                'key' => 'code_of_conduct',
                'label' => 'Código de conduta',
                'text' => Setting::get($isPj ? 'code_of_conduct_pj' : 'code_of_conduct_pf', ''),
                'version' => Setting::get($isPj ? 'code_of_conduct_version_pj' : 'code_of_conduct_version_pf', 'v1.0'),
            ],
            [
                'key' => 'integrity_policy',
                'label' => 'Política de integridade',
                'text' => Setting::get($isPj ? 'integrity_policy_pj' : 'integrity_policy_pf', ''),
                'version' => Setting::get($isPj ? 'integrity_policy_version_pj' : 'integrity_policy_version_pf', 'v1.0'),
            ],
            [
                'key' => 'data_protection',
                'label' => 'Termo de proteção de dados pessoais - LGPD',
                'text' => Setting::get($isPj ? 'data_protection_pj' : 'data_protection_pf', ''),
                'version' => Setting::get($isPj ? 'data_protection_version_pj' : 'data_protection_version_pf', 'v1.0'),
            ],
        ];

        return view('admin.submissions.print', [
            'submission' => $submission,
            'terms' => Setting::get($submission->registration_type === 'pj' ? 'terms_pj' : 'terms_pf', ''),
            'documents' => $documents,
        ]);
    }

    public function export(Request $request)
    {
        $formatParam = strtolower((string) $request->get('format'));
        $format = $formatParam === 'xlsx' ? Excel::XLSX : Excel::CSV;
        $fileName = 'form_submissions.'.($format === Excel::XLSX ? 'xlsx' : 'csv');

        $filters = $request->only(['email', 'name', 'registration_type', 'from', 'to', 'form_type']);

        return ExcelFacade::download(new FormSubmissionsExport($filters), $fileName, $format);
    }

    private function formCatalog(): array
    {
        return config('forms', []);
    }
}
