<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use App\Rules\Cnpj;
use App\Rules\Cpf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicFormController extends Controller
{
    public function listForms(): View
    {
        $forms = collect($this->formsCatalog())
            ->filter(fn ($form) => ($form['status'] ?? 'online') === 'online')
            ->map(function ($form, $slug) {
                return array_merge($form, [
                    'slug' => $slug,
                    'route' => route('forms.show', $slug),
                ]);
            })
            ->values();

        return view('form-list', compact('forms'));
    }

    public function show(string $form): View
    {
        $formConfig = $this->availableForm($form);

        return view($formConfig['view'], ['form' => $formConfig]);
    }

    public function success(string $form): View
    {
        $formConfig = $this->availableForm($form);

        return view('form-success', ['form' => $formConfig]);
    }

    public function submit(Request $request, string $form): RedirectResponse
    {
        $formConfig = $this->availableForm($form);

        $rules = $this->rulesForForm($request, $formConfig);

        $validated = $request->validate($rules);

        $investigatedFor = $validated['investigated_for'] ?? [];
        if (is_array($investigatedFor)) {
            $investigatedFor = implode(', ', $investigatedFor);
        }

        $storedDocs = [];
        foreach ($request->file('documents', []) as $file) {
            $path = $file->store('', 'private_uploads');
            $storedDocs[] = [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ];
        }

        FormSubmission::create([
            'form_type' => $formConfig['form_type'] ?? $formConfig['slug'],
            'registration_type' => $validated['registration_type'],
            'nome' => $validated['nome'],
            'cpf' => $validated['cpf'] ?? null,
            'razao_social' => $validated['razao_social'] ?? null,
            'nome_fantasia' => $validated['nome_fantasia'] ?? null,
            'cnpj' => $validated['cnpj'] ?? null,
            'representante_legal' => $validated['representante_legal'] ?? null,
            'website' => $validated['website'] ?? null,
            'endereco' => $validated['endereco'],
            'email' => $validated['email'],
            'email_testemunha' => $validated['email_testemunha'] ?? null,
            'telefone' => $validated['telefone'] ?? null,
            'nacionalidade' => $validated['nacionalidade'],
            'profissao' => $validated['profissao'],
            'data_nascimento' => $validated['data_nascimento'] ?? null,
            'dados_bancarios' => $validated['dados_bancarios'] ?? null,
            'mensagem' => $validated['mensagem'] ?? null,
            'doc_checklist' => $validated['doc_checklist'] ?? [],
            'compliance_policies' => $validated['compliance_policies'] ?? [],
            'investigated_for' => $investigatedFor,
            'investigation_details' => $validated['investigation_details'] ?? null,
            'law_12846_compliant' => $validated['law_12846_compliant'] ?? null,
            'lgpd_compliant' => $validated['lgpd_compliant'] ?? null,
            'conflict_roles' => $validated['conflict_roles'] ?? [],
            'conflict_roles_details' => $validated['conflict_roles_details'] ?? null,
            'public_power_relatives' => $validated['public_power_relatives'] ?? null,
            'public_power_relatives_details' => $validated['public_power_relatives_details'] ?? null,
            'internal_relationships' => $validated['internal_relationships'] ?? null,
            'internal_relationships_details' => $validated['internal_relationships_details'] ?? null,
            'employee_shareholding' => $validated['employee_shareholding'] ?? null,
            'employee_shareholding_details' => $validated['employee_shareholding_details'] ?? null,
            'conflict_situation' => $validated['conflict_situation'] ?? null,
            'conflict_situation_details' => $validated['conflict_situation_details'] ?? null,
            'competitor_relationships' => $validated['competitor_relationships'] ?? null,
            'competitor_relationships_details' => $validated['competitor_relationships_details'] ?? null,
            'contractor_shareholding' => $validated['contractor_shareholding'] ?? null,
            'contractor_shareholding_details' => $validated['contractor_shareholding_details'] ?? null,
            'friendship_ties' => $validated['friendship_ties'] ?? null,
            'friendship_ties_details' => $validated['friendship_ties_details'] ?? null,
            'legal_declaration' => $validated['legal_declaration'] ?? null,
            'legal_representative' => $validated['legal_representative'] ?? null,
            'legal_representative_cpf' => $validated['legal_representative_cpf'] ?? null,
            'legal_representative_role' => $validated['legal_representative_role'] ?? null,
            'legal_representative_date' => $validated['legal_representative_date'] ?? null,
            'documents' => $storedDocs,
        ]);

        return redirect()->route('forms.success', ['form' => $formConfig['slug']]);
    }

    private function rulesForForm(Request $request, array $formConfig): array
    {
        $rules = [
            'registration_type' => ['required', 'string', 'in:pj,pf'],
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => ['nullable', 'string', 'max:50', new Cpf],
            'razao_social' => ['nullable', 'string', 'max:255'],
            'nome_fantasia' => ['nullable', 'string', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:50', new Cnpj],
            'representante_legal' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'endereco' => ['required', 'string', 'max:1000'],
            'email' => ['required', 'email', 'max:255'],
            'email_testemunha' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'nacionalidade' => ['required', 'string', 'max:100'],
            'profissao' => ['required', 'string', 'max:150'],
            'data_nascimento' => ['nullable', 'date'],
            'dados_bancarios' => ['nullable', 'string', 'max:1000'],
            'mensagem' => ['nullable', 'string'],
            'doc_checklist' => ['nullable', 'array'],
            'doc_checklist.*' => ['string', 'max:255'],
            'compliance_policies' => ['nullable', 'array'],
            'compliance_policies.*' => ['string', 'max:255'],
            'investigated_for' => ['nullable', 'array'],
            'investigated_for.*' => ['string', 'max:255'],
            'investigation_details' => ['nullable', 'string'],
            'law_12846_compliant' => ['nullable', 'boolean'],
            'lgpd_compliant' => ['nullable', 'boolean'],
            'conflict_roles' => ['required', 'array'],
            'conflict_roles.*' => ['string', 'max:255'],
            'conflict_roles_details' => ['nullable', 'string'],
            'public_power_relatives' => ['required', 'string', 'in:sim,nao'],
            'public_power_relatives_details' => ['nullable', 'string'],
            'internal_relationships' => ['required', 'string', 'in:sim,nao'],
            'internal_relationships_details' => ['nullable', 'string'],
            'employee_shareholding' => ['required', 'string', 'in:sim,nao'],
            'employee_shareholding_details' => ['nullable', 'string'],
            'conflict_situation' => ['required', 'string', 'in:sim,nao'],
            'conflict_situation_details' => ['nullable', 'string'],
            'competitor_relationships' => ['required', 'string', 'in:sim,nao'],
            'competitor_relationships_details' => ['nullable', 'string'],
            'contractor_shareholding' => ['nullable', 'string', 'in:sim,nao'],
            'contractor_shareholding_details' => ['nullable', 'string'],
            'friendship_ties' => ['nullable', 'string', 'in:sim,nao'],
            'friendship_ties_details' => ['nullable', 'string'],
            'legal_declaration' => ['required', 'string', 'in:concorda,discorda'],
            'legal_representative' => ['required', 'string', 'max:255'],
            'legal_representative_cpf' => ['required', 'string', 'max:50', new Cpf],
            'legal_representative_role' => ['nullable', 'string', 'max:255'],
            'legal_representative_date' => ['required', 'date'],
            'documents' => ['required', 'array', 'min:1'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx,zip,rar,7z', 'max:15360'],
        ];

        if ($request->input('registration_type') === 'pf') {
            $rules['cpf'][0] = 'required';
        }

        if ($request->input('registration_type') === 'pj') {
            $rules['razao_social'][0] = 'required';
            $rules['nome_fantasia'][0] = 'required';
            $rules['cnpj'][0] = 'required';
            $rules['representante_legal'][0] = 'required';
            $rules['dados_bancarios'][0] = 'required';
        }

        if (!empty($formConfig['validation_rules'])) {
            $rules = array_replace_recursive($rules, $formConfig['validation_rules']);
        }

        return $rules;
    }

    private function availableForm(string $slug): array
    {
        $forms = $this->formsCatalog();
        $form = $forms[$slug] ?? null;

        if (!$form || ($form['status'] ?? 'online') !== 'online') {
            abort(404);
        }

        return array_merge($form, ['slug' => $slug]);
    }

    private function formsCatalog(): array
    {
        return config('forms', []);
    }
}
