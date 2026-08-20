<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use App\Models\Setting;
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

        return view($formConfig['view'], [
            'form' => $formConfig,
            'terms_pf' => Setting::get('terms_pf', ''),
            'terms_pj' => Setting::get('terms_pj', ''),
        ]);
    }

    public function success(string $form): View
    {
        $formConfig = $this->availableForm($form);

        return view('form-success', ['form' => $formConfig]);
    }

    public function submit(Request $request, string $form): RedirectResponse
    {
        $formConfig = $this->availableForm($form);
        $submissionContext = (string) ($request->input('submission_context') ?: $formConfig['submission_context'] ?? 'public');
        $registrationType = (string) ($request->input('tipo_pessoa') ?: $request->input('registration_type') ?: (($formConfig['restrict_registration_type'] ?? null) === 'pj' ? 'pj' : 'pf'));

        if ($submissionContext === 'rh') {
            $registrationType = 'pj';
        }

        $request->merge([
            'registration_type' => $registrationType,
            'submission_context' => $submissionContext,
        ]);

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

        $classification = $this->resolveClassification($request, $registrationType);

        $requiredDocuments = [];
        foreach ($request->file('required_documents', []) as $key => $file) {
            $path = $file->store('', 'private_uploads');
            $requiredDocuments[$key] = [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ];
        }

        FormSubmission::create([
            'verified' => false,
            'form_type' => $formConfig['form_type'] ?? $formConfig['slug'],
            'registration_type' => $registrationType,
            'classification' => $classification,
            'nome' => $validated['nome'],
            'cpf' => $validated['cpf'] ?? null,
            'razao_social' => $validated['razao_social'] ?? null,
            'nome_fantasia' => $validated['nome_fantasia'] ?? null,
            'cnpj' => $validated['cnpj'] ?? null,
            'representante_legal' => $validated['representante_legal'] ?? $validated['representante_legal_nome'] ?? null,
            'representante_legal_nome' => $validated['representante_legal_nome'] ?? null,
            'representante_legal_email' => $validated['representante_legal_email'] ?? null,
            'responsavel_juridico_nome' => $validated['responsavel_juridico_nome'] ?? null,
            'responsavel_juridico_email' => $validated['responsavel_juridico_email'] ?? null,
            'website' => $validated['website'] ?? null,
            'endereco' => $validated['endereco'],
            'email' => $validated['email'],
            'email_testemunha' => $validated['testemunha_email'] ?? $validated['email_testemunha'] ?? null,
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
            'testemunha_nome' => $validated['testemunha_nome'] ?? null,
            'testemunha_email' => $validated['testemunha_email'] ?? null,
            'compliance_aceito_em' => now(),
            'documents' => $storedDocs,
            'required_documents' => $requiredDocuments,
        ]);

        return redirect()->route('forms.success', ['form' => $formConfig['slug']]);
    }

    private function resolveClassification(Request $request, string $registrationType): ?string
    {
        $source = strtolower((string) ($request->input('submission_context') ?: $request->input('source') ?: 'public'));

        if ($source === 'rh') {
            return 'pj-rh';
        }

        if ($registrationType === 'pj') {
            return 'pj';
        }

        if ($registrationType === 'pf') {
            return 'pf';
        }

        return null;
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
            'representante_legal_nome' => ['nullable', 'string', 'max:255'],
            'representante_legal_email' => ['nullable', 'email', 'max:255'],
            'responsavel_juridico_nome' => ['nullable', 'string', 'max:255'],
            'responsavel_juridico_email' => ['nullable', 'email', 'max:255'],
            'testemunha_nome' => ['nullable', 'string', 'max:255'],
            'testemunha_email' => ['nullable', 'email', 'max:255'],
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
            'required_documents' => ['required', 'array'],
            'required_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:15360'],
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
            'terms_accepted' => ['required', 'accepted'],
            'compliance_aceito_em' => ['prohibited'],
            'documents' => ['required', 'array', 'min:1'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx,zip,rar,7z', 'max:15360'],
        ];

        $submissionContext = (string) ($request->input('submission_context') ?: $formConfig['submission_context'] ?? 'public');
        $registrationType = (string) ($request->input('tipo_pessoa') ?: $request->input('registration_type') ?: (($formConfig['restrict_registration_type'] ?? null) === 'pj' ? 'pj' : 'pf'));

        if ($submissionContext === 'rh') {
            $registrationType = 'pj';
        }

        if ($registrationType === 'pf') {
            $rules['cpf'][0] = 'required';
            $rules['testemunha_nome'][0] = 'required';
            $rules['testemunha_email'][0] = 'required';
            $rules['testemunha_email'][1] = 'email';
            $rules['required_documents.personal_documents'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:15360'];
        }

        if ($registrationType === 'pj') {
            $rules['razao_social'][0] = 'required';
            $rules['nome_fantasia'][0] = 'required';
            $rules['cnpj'][0] = 'required';
            $rules['representante_legal_nome'][0] = 'required';
            $rules['representante_legal_email'][0] = 'required';
            $rules['testemunha_nome'][0] = 'required';
            $rules['testemunha_email'][0] = 'required';
            $rules['testemunha_email'][1] = 'email';
            $rules['dados_bancarios'][0] = 'required';
            $rules['required_documents.corporate_document'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:15360'];
            $rules['required_documents.legal_representative_document'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:15360'];
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
