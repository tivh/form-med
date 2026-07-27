<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplianceDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ComplianceDocumentController extends Controller
{
    public function index(): View
    {
        $documents = ComplianceDocument::query()
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.compliance.index', ['documents' => $documents]);
    }

    public function create(): View
    {
        return view('admin.compliance.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category'    => ['nullable', 'string', 'max:100'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
            'file'        => ['required', 'file', 'max:51200', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip'],
        ]);

        $file = $request->file('file');
        $path = $file->store('compliance', 'private_uploads');

        ComplianceDocument::create([
            'title'              => $data['title'],
            'description'        => $data['description'] ?? null,
            'category'           => $data['category'] ?? null,
            'sort_order'         => $data['sort_order'] ?? 0,
            'is_active'          => isset($data['is_active']) ? (bool) $data['is_active'] : true,
            'file_path'          => $path,
            'file_original_name' => $file->getClientOriginalName(),
            'file_size'          => $file->getSize(),
            'mime_type'          => $file->getMimeType(),
        ]);

        return redirect()
            ->route('admin.compliance.index')
            ->with('status', 'Documento adicionado com sucesso.');
    }

    public function edit(ComplianceDocument $complianceDocument): View
    {
        return view('admin.compliance.edit', ['document' => $complianceDocument]);
    }

    public function update(Request $request, ComplianceDocument $complianceDocument): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category'    => ['nullable', 'string', 'max:100'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
            'file'        => ['nullable', 'file', 'max:51200', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip'],
        ]);

        $updateData = [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'category'    => $data['category'] ?? null,
            'sort_order'  => $data['sort_order'] ?? 0,
            'is_active'   => isset($data['is_active']) ? (bool) $data['is_active'] : false,
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            if (Storage::disk('private_uploads')->exists($complianceDocument->file_path)) {
                Storage::disk('private_uploads')->delete($complianceDocument->file_path);
            }

            $file = $request->file('file');
            $updateData['file_path']          = $file->store('compliance', 'private_uploads');
            $updateData['file_original_name'] = $file->getClientOriginalName();
            $updateData['file_size']          = $file->getSize();
            $updateData['mime_type']          = $file->getMimeType();
        }

        $complianceDocument->update($updateData);

        return redirect()
            ->route('admin.compliance.index')
            ->with('status', 'Documento atualizado com sucesso.');
    }

    public function destroy(ComplianceDocument $complianceDocument): RedirectResponse
    {
        if (Storage::disk('private_uploads')->exists($complianceDocument->file_path)) {
            Storage::disk('private_uploads')->delete($complianceDocument->file_path);
        }

        $complianceDocument->delete();

        return redirect()
            ->route('admin.compliance.index')
            ->with('status', 'Documento excluído com sucesso.');
    }

    public function download(ComplianceDocument $complianceDocument): Response
    {
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
