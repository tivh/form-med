@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-10">
    <div class="glass rounded-3xl p-10 shadow-xl border border-white/70">
        @php
            $formTitle = $form['title'] ?? 'QUESTIONÁRIO INICIAL PARA SOLICITAÇÃO DE CADASTRO';
            $formDescription = $form['description'] ?? 'SGC - R 1-03-1 - Revisão:07  - Emissão: 24/02/2026.';
            $formSlug = $form['slug'] ?? 'form-med';
        @endphp
        <div class="flex items-start justify-between mb-10">
            <div class="space-y-3">
                <div class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 border border-red-100">Envio seguro</div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 leading-tight">{{ $formTitle }}</h1>
                <p class="text-slate-600">{{ $formDescription }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 flex items-start space-x-3">
                <svg class="h-5 w-5 mt-0.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L9 9.586 7.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                <div>
                    <p class="font-semibold">Tudo certo!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                <p class="font-semibold mb-2">Revise os campos abaixo:</p>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('forms.submit', ['form' => $formSlug]) }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="submission-form">
            @csrf

            <div class="flex items-center gap-3 rounded-2xl border border-red-100 bg-red-50/80 p-4 text-sm font-semibold">
                <button type="button" data-step-indicator="1" data-go-step="1" class="step-pill inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-4 py-2 text-red-900 shadow-sm transition cursor-pointer">Dados cadastrais</button>
                <button type="button" data-step-indicator="2" data-go-step="2" class="step-pill inline-flex items-center gap-2 rounded-full border border-red-200 bg-red-50 px-4 py-2 text-red-900 shadow-sm transition cursor-pointer">Compliance e Conflito de Interesses</button>
            </div>

            <div id="step-1" data-step="1" class="space-y-8">
                <div class="rounded-2xl border border-slate-100 bg-white/90 p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-2" data-question>
                        <span class="question-number"></span>
                        <p class="block text-sm font-semibold text-slate-800">Tipo de cadastro</p>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center gap-2 text-slate-900 text-sm font-medium px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:border-red-200 hover:bg-red-50 transition">
                            <input type="radio" name="registration_type" value="pj" {{ old('registration_type') === 'pj' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                            <span>Pessoa Jurídica</span>
                        </label>
                        <label class="inline-flex items-center gap-2 text-slate-900 text-sm font-medium px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:border-red-200 hover:bg-red-50 transition">
                            <input type="radio" name="registration_type" value="pf" {{ old('registration_type') === 'pf' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                            <span>Pessoa Física</span>
                        </label>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-white/90 p-6 shadow-sm" id="fields-common" data-step="1">
                    <div class="space-y-6">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
                            <div class="mb-4 flex items-center gap-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-sm font-semibold text-red-700">1</span>
                                <h2 class="text-base font-semibold text-slate-900">{{ old('registration_type') === 'pj' ? 'Identificação da empresa' : 'Identificação pessoal' }}</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm pf-only">
                                    <label for="nome_pf" class="block text-sm font-semibold text-slate-900">Nome completo</label>
                                    <input type="text" name="nome" id="nome_pf" value="{{ old('nome') }}" required
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Seu nome completo" />
                                </div>
                                <input type="hidden" name="nome" id="nome_pj_hidden" class="pj-only" value="{{ old('nome') }}">

                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm pj-only">
                                    <label for="razao_social" class="block text-sm font-semibold text-slate-900">Razão social</label>
                                    <input type="text" name="razao_social" id="razao_social" value="{{ old('razao_social') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                                </div>
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm pj-only">
                                    <label for="nome_fantasia" class="block text-sm font-semibold text-slate-900">Nome fantasia</label>
                                    <input type="text" name="nome_fantasia" id="nome_fantasia" value="{{ old('nome_fantasia') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                                </div>
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm pj-only">
                                    <label for="cnpj" class="block text-sm font-semibold text-slate-900">CNPJ</label>
                                    <input type="text" name="cnpj" id="cnpj" value="{{ old('cnpj') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="00.000.000/0000-00" />
                                    <p id="cnpj-error" class="text-xs text-red-600 hidden">CNPJ inválido.</p>
                                </div>
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm pf-only">
                                    <label for="cpf" class="block text-sm font-semibold text-slate-900">CPF</label>
                                    <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="000.000.000-00" />
                                    <p id="cpf-error" class="text-xs text-red-600 hidden">CPF inválido.</p>
                                </div>
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                    <label for="telefone" class="block text-sm font-semibold text-slate-900">Telefone</label>
                                    <input type="text" name="telefone" id="telefone" value="{{ old('telefone') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="(11) 99999-9999" />
                                </div>
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                    <label for="endereco" class="block text-sm font-semibold text-slate-900">Endereço</label>
                                    <input type="text" name="endereco" id="endereco" value="{{ old('endereco') }}" required
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                                </div>
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                    <label for="email" class="block text-sm font-semibold text-slate-900">E-mail</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="voce@email.com" />
                                </div>
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm pj-only">
                                    <label for="website" class="block text-sm font-semibold text-slate-900">Website</label>
                                    <input type="text" name="website" id="website" value="{{ old('website') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="https://" />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 pj-only">
                            <div class="mb-4 flex items-center gap-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-sm font-semibold text-red-700">2</span>
                                <h2 class="text-base font-semibold text-slate-900">Representante legal</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                    <label for="representante_legal_nome" class="block text-sm font-semibold text-slate-900">Nome do representante legal</label>
                                    <input type="text" name="representante_legal_nome" id="representante_legal_nome" value="{{ old('representante_legal_nome') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                                </div>
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                    <label for="representante_legal_email" class="block text-sm font-semibold text-slate-900">E-mail do representante legal</label>
                                    <input type="email" name="representante_legal_email" id="representante_legal_email" value="{{ old('representante_legal_email') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="representante@email.com" />
                                </div>
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                    <label for="nacionalidade" class="block text-sm font-semibold text-slate-900">Nacionalidade</label>
                                    <input type="text" name="nacionalidade" id="nacionalidade" value="{{ old('nacionalidade') }}" required
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                                </div>
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                    <label for="profissao" class="block text-sm font-semibold text-slate-900">Profissão</label>
                                    <input type="text" name="profissao" id="profissao" value="{{ old('profissao') }}" required
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 pj-only">
                            <div class="mb-4 flex items-center gap-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-sm font-semibold text-red-700">3</span>
                                <h2 class="text-base font-semibold text-slate-900">Responsável jurídico</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                    <label for="responsavel_juridico_nome" class="block text-sm font-semibold text-slate-900">Nome do responsável jurídico</label>
                                    <input type="text" name="responsavel_juridico_nome" id="responsavel_juridico_nome" value="{{ old('responsavel_juridico_nome') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                                </div>
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                    <label for="responsavel_juridico_email" class="block text-sm font-semibold text-slate-900">E-mail do responsável jurídico</label>
                                    <input type="email" name="responsavel_juridico_email" id="responsavel_juridico_email" value="{{ old('responsavel_juridico_email') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="responsavel@email.com" />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
                            <div class="mb-4 flex items-center gap-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-sm font-semibold text-red-700">{{ old('registration_type') === 'pj' ? '4' : '2' }}</span>
                                <h2 class="text-base font-semibold text-slate-900">Testemunha</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                    <label for="testemunha_nome" class="block text-sm font-semibold text-slate-900">Nome da testemunha</label>
                                    <input type="text" name="testemunha_nome" id="testemunha_nome" value="{{ old('testemunha_nome') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                                </div>
                                <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                    <label for="testemunha_email" class="block text-sm font-semibold text-slate-900">E-mail da testemunha</label>
                                    <input type="email" name="testemunha_email" id="testemunha_email" value="{{ old('testemunha_email') }}"
                                        class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="testemunha@email.com" />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
                            <div class="mb-4 flex items-center gap-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-sm font-semibold text-red-700">{{ old('registration_type') === 'pj' ? '5' : '3' }}</span>
                                <h2 class="text-base font-semibold text-slate-900">Dados financeiros</h2>
                            </div>
                            <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                <label for="dados_bancarios" class="block text-sm font-semibold text-slate-900">Dados bancários</label>
                                <textarea name="dados_bancarios" id="dados_bancarios" rows="2" class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Banco, agência, conta ou chave PIX">{{ old('dados_bancarios') }}</textarea>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 pf-only">
                            <div class="mb-4 flex items-center gap-2">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-100 text-sm font-semibold text-red-700">4</span>
                                <h2 class="text-base font-semibold text-slate-900">Data de nascimento</h2>
                            </div>
                            <div class="space-y-2 rounded-xl border border-slate-100 bg-white/80 p-4 shadow-sm">
                                <label for="data_nascimento" class="block text-sm font-semibold text-slate-900">Data de nascimento</label>
                                <input type="date" name="data_nascimento" id="data_nascimento" value="{{ old('data_nascimento') }}"
                                    class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $docOptionsPj = [
                        'Cartão do CNPJ',
                        'Ata de nomeação ou procuração com poderes específicos de representação',
                        'Qualificação técnica profissional ativa (ex. CRM)',
                        'Certificação de especialização profissional',
                        'Alvará de funcionamento (válido)',
                        'Alvará sanitário (válido)',
                        'Licenças ambientais (municipais, estaduais e federais)',
                        'Certidão negativa distribuidores cíveis/criminais (Estadual)',
                        'Certidão negativa distribuidores cíveis/criminais (Federal)',
                        'Certidões de inexistência/distribuição procedimentos extrajudiciais (MPF)',
                        'Certidões de inexistência/distribuição procedimentos extrajudiciais (MPE)',
                        'Certificado de Responsabilidade Técnica',
                        'Contrato com a VH ou minuta',
                    ];
                    $docOptionsPf = [
                        'Documentação de qualificação técnica profissional ativo (ex. CRM)',
                        'Certificação de especialização profissional (ex. diploma, certificação)',
                        'Minuta contratual (caso não tenha contrato formal para prestação de serviço, sinalizar)',
                    ];
                @endphp

                <div class="rounded-2xl border border-slate-100 bg-white/90 p-6 shadow-sm space-y-4" data-step="1">
                    <div class="rounded-2xl border border-red-200 bg-red-50/60 p-5 shadow-sm pf-only">
                        <div class="flex items-center gap-2" data-question>
                            <span class="question-number"></span>
                            <label for="personal_documents" class="text-sm font-semibold text-slate-900">Documentos pessoais (CNH ou RG) <span class="text-red-600">*</span></label>
                        </div>
                        <p class="mt-2 text-xs text-slate-600">Envie o documento pessoal obrigatório em PDF, JPG, PNG, DOC ou DOCX, com até 15MB.</p>
                        <input type="file" name="required_documents[personal_documents]" id="personal_documents" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="mt-3 block w-full text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-red-600 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-red-700" />
                    </div>

                    <div class="rounded-2xl border border-red-200 bg-red-50/60 p-5 shadow-sm space-y-5 pj-only">
                        <div>
                            <div class="flex items-center gap-2" data-question>
                                <span class="question-number"></span>
                                <label for="corporate_document" class="text-sm font-semibold text-slate-900">Contrato social ou Estatuto social <span class="text-red-600">*</span></label>
                            </div>
                            <p class="mt-2 text-xs text-slate-600">Envie um dos dois documentos em PDF, JPG, PNG, DOC ou DOCX, com até 15MB.</p>
                            <input type="file" name="required_documents[corporate_document]" id="corporate_document" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="mt-3 block w-full text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-red-600 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-red-700" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2" data-question>
                                <span class="question-number"></span>
                                <label for="legal_representative_document" class="text-sm font-semibold text-slate-900">Documento do representante legal (CNH ou RG) <span class="text-red-600">*</span></label>
                            </div>
                            <p class="mt-2 text-xs text-slate-600">Envie o documento do representante em PDF, JPG, PNG, DOC ou DOCX, com até 15MB.</p>
                            <input type="file" name="required_documents[legal_representative_document]" id="legal_representative_document" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="mt-3 block w-full text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-red-600 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-red-700" />
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm pf-only">
                        <div class="flex items-center gap-2" data-question>
                            <span class="question-number"></span>
                            <p class="text-sm font-semibold text-slate-900">Documentação solicitada (PF)</p>
                        </div>
                        <div class="mt-3 space-y-3 text-sm text-slate-800">
                            @foreach ($docOptionsPf as $option)
                                <label class="flex items-start gap-2">
                                    <input type="checkbox" name="doc_checklist[]" value="{{ $option }}" class="mt-1 text-red-600 border-slate-300" {{ in_array($option, old('doc_checklist', [])) ? 'checked' : '' }}>
                                    <span>{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm pj-only">
                        <div class="flex items-center gap-2" data-question>
                            <span class="question-number"></span>
                            <p class="text-sm font-semibold text-slate-900">Documentação solicitada (PJ)</p>
                        </div>
                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach ($docOptionsPj as $option)
                                <label class="inline-flex items-start space-x-2 text-sm text-slate-700">
                                    <input type="checkbox" name="doc_checklist[]" value="{{ $option }}" {{ in_array($option, old('doc_checklist', [])) ? 'checked' : '' }} class="mt-0.5 text-red-600 border-slate-300">
                                    <span>{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-2" data-question>
                            <span class="question-number"></span>
                            <label class="block text-sm font-semibold text-slate-900">Documentação (PDF, JPG, PNG, DOC, ZIP, RAR ou 7Z até 15MB)</label>
                        </div>
                        <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 px-4 py-5 text-slate-700">
                            <input type="file" name="documents[]" id="documents" multiple required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.zip,.rar,.7z"
                                class="block w-full text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-red-600 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-red-700" />
                            <p class="mt-2 text-xs text-slate-500">Anexe todos os documentos solicitados (pode selecionar vários de uma vez). Máx. 15MB por arquivo. Aceita arquivos compactados (ZIP, RAR, 7Z).</p>
                            <p id="documents-size-error" class="mt-2 text-sm font-semibold text-red-600 hidden"></p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-2" data-question>
                            <span class="question-number"></span>
                            <label for="mensagem" class="block text-sm font-semibold text-slate-900">Observações adicionais</label>
                        </div>
                        <textarea name="mensagem" id="mensagem" rows="4" class="block w-full rounded-2xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Conte-nos mais...">{{ old('mensagem') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" id="next-step" class="inline-flex items-center px-6 py-3 rounded-xl bg-red-600 text-white font-semibold shadow-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 focus:ring-offset-white transition">Avançar para conflitos</button>
                </div>
            </div>

            <div id="step-2" data-step="2" class="space-y-6 hidden">
                <div class="space-y-4" data-step="2">
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                        <div class="mt-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                            A data de submissão será registrada automaticamente no envio: <strong>{{ now()->format('d/m/Y') }}</strong>
                        </div>
                    </div>

                    <!-- Pergunta 1 -->
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">1</span>
                            <p class="text-sm font-semibold text-slate-900">A PJ/PF possui algum dos seguintes documentos ou programas?</p>
                        </div>
                        <p class="text-xs text-slate-600 mb-3 italic">(Marque todas as opções aplicáveis.)</p>
                        @php
                            $policyOptions = [
                                'Código de Ética ou Conduta',
                                'Programa de Compliance estruturado',
                                'Canal de Denúncias',
                                'Política Anticorrupção',
                                'Política de Conflito de Interesses',
                                'Política de Proteção de Dados (LGPD)',
                            ];
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach ($policyOptions as $option)
                                <label class="inline-flex items-start space-x-2 text-sm text-slate-700">
                                    <input type="checkbox" name="compliance_policies[]" value="{{ $option }}" {{ in_array($option, old('compliance_policies', [])) ? 'checked' : '' }} class="mt-0.5 text-red-600 border-slate-300">
                                    <span>{{ $option }}</span>
                                </label>
                            @endforeach
                            <label class="inline-flex items-start space-x-2 text-sm text-slate-700">
                                <input type="checkbox" name="compliance_policies[]" value="Nenhum" {{ in_array('Nenhum', old('compliance_policies', [])) ? 'checked' : '' }} class="mt-0.5 text-red-600 border-slate-300">
                                <span>Nenhum dos itens acima</span>
                            </label>
                        </div>
                    </div>

                    <!-- Pergunta 2 -->
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">2</span>
                            <label class="text-sm font-semibold text-slate-900">A PJ/PF declara estar em conformidade com a Lei nº 12.846/2013 (Lei Anticorrupção)?</label>
                        </div>
                        <div class="flex items-center space-x-6 text-sm text-slate-700">
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="law_12846_compliant" value="1" {{ old('law_12846_compliant') === '1' ? 'checked' : '' }} class="text-red-600 border-slate-300">
                                <span>Sim</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="law_12846_compliant" value="0" {{ old('law_12846_compliant') === '0' ? 'checked' : '' }} class="text-red-600 border-slate-300">
                                <span>Não</span>
                            </label>
                        </div>
                    </div>

                    <!-- Pergunta 3 -->
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">3</span>
                            <label class="text-sm font-semibold text-slate-900">A PJ/PF declara estar em conformidade com a Lei nº 13.709/2018 (Lei Geral de Proteção de Dados – LGPD)?</label>
                        </div>
                        <div class="flex items-center space-x-6 text-sm text-slate-700">
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="lgpd_compliant" value="1" {{ old('lgpd_compliant') === '1' ? 'checked' : '' }} class="text-red-600 border-slate-300">
                                <span>Sim</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="lgpd_compliant" value="0" {{ old('lgpd_compliant') === '0' ? 'checked' : '' }} class="text-red-600 border-slate-300">
                                <span>Não</span>
                            </label>
                        </div>
                    </div>

                    <!-- Pergunta 4 -->
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">4</span>
                            <p class="text-sm font-semibold text-slate-900">A PJ/PF ou, no caso de pessoa jurídica, algum de seus sócios, administradores ou representantes, já foi investigado ou condenado por algum dos fatos abaixo?</p>
                        </div>
                        <p class="text-xs text-slate-600 mb-3 italic">(Marque todas as opções aplicáveis.)</p>
                        @php
                            $investigationOptions = ['Corrupção','Fraude','Lavagem de dinheiro','Crimes ambientais','Infrações trabalhistas graves','Não'];
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach ($investigationOptions as $opt)
                                <label class="inline-flex items-start space-x-2 text-sm text-slate-700">
                                    <input type="checkbox" name="investigated_for[]" value="{{ $opt }}" {{ in_array($opt, old('investigated_for', [])) ? 'checked' : '' }} class="mt-0.5 text-red-600 border-slate-300">
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-4 space-y-2">
                            <label class="block text-sm font-semibold text-slate-800" for="conflict_roles_details">Caso tenha assinalado qualquer opção diferente de "Não", descreva os detalhes:</label>
                            <textarea name="conflict_roles_details" id="conflict_roles_details" rows="3" class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-400 focus:ring-red-400" placeholder="Descreva os detalhes...">{{ old('conflict_roles_details') }}</textarea>
                        </div>
                    </div>

                    <!-- Pergunta 5 -->
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">5</span>
                            <p class="text-sm font-semibold text-slate-900">A PJ/PF ou, no caso de pessoa jurídica, algum de seus sócios, administradores ou representantes, enquadra-se em alguma das situações abaixo?</p>
                        </div>
                        <p class="text-xs text-slate-600 mb-3 italic">(Marque todas as opções aplicáveis.)</p>
                        @php
                            $conflictRoleOptions = [
                                'Agente Público',
                                'Ex-Agente Público (nos últimos 5 anos)',
                                'Pessoa Politicamente Exposta (PPE)',
                                'Parente até o 3º grau de Agente Público',
                                'Nenhuma das opções acima',
                            ];
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach ($conflictRoleOptions as $option)
                                <label class="inline-flex items-start space-x-2 text-sm text-slate-700">
                                    <input type="checkbox" name="conflict_roles[]" value="{{ $option }}" {{ in_array($option, old('conflict_roles', [])) ? 'checked' : '' }} class="mt-0.5 text-red-600 border-slate-300">
                                    <span>{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-4 space-y-2">
                            <label class="block text-sm font-semibold text-slate-800" for="conflict_roles_details">Caso positivo, informe os detalhes:</label>
                            <textarea name="conflict_roles_details_q5" id="conflict_roles_details_q5" rows="3" class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-400 focus:ring-red-400" placeholder="Informe os detalhes...">{{ old('conflict_roles_details_q5') }}</textarea>
                        </div>
                    </div>

                    <!-- Pergunta 6 -->
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">6</span>
                            <p class="text-sm font-semibold text-slate-900">A PJ/PF ou, no caso de pessoa jurídica, algum de seus sócios, administradores ou representantes, ocupa cargo ou mantém vínculo familiar com pessoa em posição de decisão em órgão ou entidade pública que possua relação com a Vitória Hospitalar?</p>
                        </div>
                        <div class="flex items-center space-x-6 text-sm text-slate-700 mb-4">
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="public_power_relatives" value="sim" {{ old('public_power_relatives') === 'sim' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                                <span>Sim</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="public_power_relatives" value="nao" {{ old('public_power_relatives') === 'nao' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                                <span>Não</span>
                            </label>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-800" for="public_power_relatives_details">Se sim, informe o órgão, cargo e/ou nome do servidor:</label>
                            <textarea name="public_power_relatives_details" id="public_power_relatives_details" rows="3" class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-400 focus:ring-red-400" placeholder="Informe os detalhes...">{{ old('public_power_relatives_details') }}</textarea>
                        </div>
                    </div>

                    <!-- Pergunta 7 -->
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">7</span>
                            <p class="text-sm font-semibold text-slate-900">A PJ/PF possui relacionamento pessoal, familiar ou comercial com sócios, administradores ou colaboradores da Vitória Hospitalar?</p>
                        </div>
                        <div class="flex items-center space-x-6 text-sm text-slate-700 mb-4">
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="internal_relationships" value="sim" {{ old('internal_relationships') === 'sim' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                                <span>Sim</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="internal_relationships" value="nao" {{ old('internal_relationships') === 'nao' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                                <span>Não</span>
                            </label>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-800" for="internal_relationships_details">Se sim, informe a pessoa, área e a natureza do relacionamento:</label>
                            <textarea name="internal_relationships_details" id="internal_relationships_details" rows="3" class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-400 focus:ring-red-400" placeholder="Informe os detalhes...">{{ old('internal_relationships_details') }}</textarea>
                        </div>
                    </div>

                    <!-- Pergunta 8 -->
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">8</span>
                            <p class="text-sm font-semibold text-slate-900">A PJ/PF ou, no caso de pessoa jurídica, seus sócios ou administradores, possuem participação societária, direta ou indireta, na Vitória Hospitalar?</p>
                        </div>
                        <div class="flex items-center space-x-6 text-sm text-slate-700 mb-4">
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="employee_shareholding" value="sim" {{ old('employee_shareholding') === 'sim' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                                <span>Sim</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="employee_shareholding" value="nao" {{ old('employee_shareholding') === 'nao' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                                <span>Não</span>
                            </label>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-800" for="employee_shareholding_details">Se sim, informe os detalhes:</label>
                            <textarea name="employee_shareholding_details" id="employee_shareholding_details" rows="3" class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-400 focus:ring-red-400" placeholder="Informe os detalhes...">{{ old('employee_shareholding_details') }}</textarea>
                        </div>
                    </div>

                    <!-- Pergunta 9 -->
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">9</span>
                            <p class="text-sm font-semibold text-slate-900">A PJ/PF possui relacionamento pessoal, familiar ou comercial com sócios, administradores ou colaboradores de empresas concorrentes da Vitória Hospitalar?</p>
                        </div>
                        <div class="flex items-center space-x-6 text-sm text-slate-700 mb-4">
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="competitor_relationships" value="sim" {{ old('competitor_relationships') === 'sim' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                                <span>Sim</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="competitor_relationships" value="nao" {{ old('competitor_relationships') === 'nao' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                                <span>Não</span>
                            </label>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-slate-800" for="competitor_relationships_details">Se sim, informe os detalhes:</label>
                            <textarea name="competitor_relationships_details" id="competitor_relationships_details" rows="3" class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-400 focus:ring-red-400" placeholder="Informe os detalhes...">{{ old('competitor_relationships_details') }}</textarea>
                        </div>
                    </div>

                    <!-- Pergunta 10 -->
                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white text-sm font-bold">10</span>
                            <p class="text-sm font-semibold text-slate-900">Existe qualquer situação envolvendo a PJ/PF que possa caracterizar conflito de interesses real, potencial ou aparente em relação à Vitória Hospitalar?</p>
                        </div>
                        <div class="flex items-center space-x-6 text-sm text-slate-700">
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="conflict_situation" value="sim" {{ old('conflict_situation') === 'sim' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                                <span>Sim</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="conflict_situation" value="nao" {{ old('conflict_situation') === 'nao' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                                <span>Não</span>
                            </label>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white/90 p-5 shadow-sm">
                        <div class="flex items-center gap-2" data-question>
                            <span class="question-number"></span>
                            <label class="block text-sm font-semibold text-slate-900">Declaro, sob as penas da lei, que:</label>
                        </div>
                        <ul class="mt-3 space-y-2 text-sm text-slate-800 list-disc list-inside">
                            <li>As informações prestadas são verdadeiras.</li>
                            <li>Não pratico atos lesivos contra a Administração Pública.</li>
                            <li>Não realizo pagamento ou oferecimento de vantagem indevida a agentes públicos ou privados.</li>
                            <li>Não ofereço vantagens indevidas a profissionais da saúde.</li>
                            <li>Não realizo indução comercial em desacordo com boas práticas médicas.</li>
                            <li>Não pratico qualquer ato que viole normas sanitárias.</li>
                            <li>Cumpro integralmente a legislação e normas éticas, sanitárias, trabalhistas, ambientais e tributárias vigentes.</li>
                            <li>Comprometo a comunicar imediatamente qualquer alteração nas informações aqui prestadas.</li>
                            <li>Autorizo a empresa a realizar verificações reputacionais e consultas em bases públicas.</li>
                        </ul>
                        <div class="mt-4 flex items-center space-x-4 text-sm text-slate-700">
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="legal_declaration" value="concorda" {{ old('legal_declaration') === 'concorda' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                                <span>Manifesto minha expressa concordância</span>
                            </label>
                            <label class="inline-flex items-center space-x-2">
                                <input type="radio" name="legal_declaration" value="discorda" {{ old('legal_declaration') === 'discorda' ? 'checked' : '' }} required class="text-red-600 border-slate-300">
                                <span>Manifesto minha expressa discordância</span>
                            </label>
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-800" for="legal_representative">Nome do Responsável Legal</label>
                                <input type="text" name="legal_representative" id="legal_representative" value="{{ old('legal_representative') }}" required class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-400 focus:ring-red-400" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-800" for="legal_representative_cpf">CPF</label>
                                <input type="text" name="legal_representative_cpf" id="legal_representative_cpf" value="{{ old('legal_representative_cpf') }}" required class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-400 focus:ring-red-400" placeholder="000.000.000-00" />
                                <p id="legal-cpf-error" class="text-xs text-red-600 hidden">CPF inválido.</p>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-800" for="legal_representative_role">Cargo</label>
                                <input type="text" name="legal_representative_role" id="legal_representative_role" value="{{ old('legal_representative_role') }}" class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-400 focus:ring-red-400" />
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-slate-800" for="legal_representative_date">Data</label>
                                <input type="date" name="legal_representative_date" id="legal_representative_date" value="{{ old('legal_representative_date') }}" required class="block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-400 focus:ring-red-400" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Termos e Condições -->
                @if($terms_pf || $terms_pj)
                <div class="rounded-2xl border border-slate-100 bg-white/90 p-6 shadow-sm space-y-4" id="terms-section">
                    <div class="flex items-center gap-2" data-question>
                        <span class="question-number"></span>
                        <p class="block text-sm font-semibold text-slate-800">Termos e Condições</p>
                    </div>
                    <label class="inline-flex items-start gap-3 cursor-pointer group">
                        <input
                            type="checkbox"
                            name="terms_accepted"
                            id="terms_accepted"
                            value="1"
                            {{ old('terms_accepted') ? 'checked' : '' }}
                            class="mt-0.5 h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500 flex-shrink-0"
                        >
                        <span class="text-sm text-slate-800 leading-relaxed">
                            Li e aceito os
                            <button type="button" id="open-terms-modal" class="font-semibold text-red-600 underline underline-offset-2 hover:text-red-800 transition">termos e condições</button>
                            referentes a este cadastro.
                        </span>
                    </label>
                    @error('terms_accepted')
                        <p class="text-xs text-red-600">Você precisa aceitar os termos para continuar.</p>
                    @enderror
                </div>
                @endif

                <div class="flex items-center justify-between">
                    <button type="button" id="previous-step" class="inline-flex items-center px-5 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold shadow-sm hover:border-red-200 hover:text-red-800 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 focus:ring-offset-white transition">Voltar</button>
                    <button type="submit" class="inline-flex items-center px-6 py-3 rounded-xl bg-red-600 text-white font-semibold shadow-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 focus:ring-offset-white transition">Enviar formulário</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Termos -->
@if($terms_pf || $terms_pj)
<div id="terms-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="terms-modal-backdrop"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <span id="terms-modal-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700"></span>
                <h2 class="text-lg font-bold text-slate-900">Termos e Condições</h2>
            </div>
            <button type="button" id="close-terms-modal" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1">
            <pre id="terms-modal-content" class="whitespace-pre-wrap text-sm text-slate-800 font-sans leading-relaxed"></pre>
        </div>
        <div class="p-4 border-t border-slate-200 flex justify-end">
            <button type="button" id="accept-terms-btn" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Aceitar e fechar
            </button>
        </div>
    </div>
</div>
@endif

<script>
    (function() {
        const form = document.getElementById('submission-form');
        if (!form) return;

        const stepPanels = [1, 2]
            .map((step) => document.getElementById(`step-${step}`))
            .filter(Boolean);
        const indicators = Array.from(form.querySelectorAll('[data-step-indicator]'));
        const nextBtn = document.getElementById('next-step');
        const prevBtn = document.getElementById('previous-step');
        let currentStep = 1;

        const getRegistrationType = () => form.querySelector('input[name="registration_type"]:checked')?.value;

        const isEnabledForType = (node) => {
            const type = getRegistrationType();
            if (node.closest('.pj-only')) return type === 'pj';
            if (node.closest('.pf-only')) return type === 'pf';
            return true;
        };

        const setQuestionNumbers = () => {
            let counter = 1;
            const questions = Array.from(form.querySelectorAll('[data-question]')).filter(isEnabledForType);
            questions.forEach((question) => {
                let badge = question.querySelector('.question-number');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'question-number inline-flex h-7 w-7 items-center justify-center rounded-full bg-red-50 text-red-800 text-xs font-semibold border border-red-200 flex-shrink-0';
                    question.prepend(badge);
                }
                badge.textContent = `${counter}.`;
                counter += 1;
            });
        };

        const conflictOptions = Array.from(document.querySelectorAll('.conflict-role-option'));
        const noneOption = conflictOptions.find((opt) => opt.value === 'Nenhuma');
        if (noneOption) {
            noneOption.addEventListener('change', () => {
                if (noneOption.checked) {
                    conflictOptions.forEach((opt) => {
                        if (opt !== noneOption) opt.checked = false;
                    });
                }
            });
            conflictOptions.forEach((opt) => {
                if (opt === noneOption) return;
                opt.addEventListener('change', () => {
                    if (opt.checked) noneOption.checked = false;
                });
            });
        }

        const updateIndicators = () => {
            indicators.forEach((indicator) => {
                const isActive = Number(indicator.dataset.stepIndicator) === currentStep;
                indicator.setAttribute('aria-current', isActive ? 'step' : 'false');
                indicator.classList.toggle('bg-red-600', isActive);
                indicator.classList.toggle('text-white', isActive);
                indicator.classList.toggle('border-red-600', isActive);
                indicator.classList.toggle('bg-red-50', !isActive);
                indicator.classList.toggle('text-red-900', !isActive);
                indicator.classList.toggle('border-red-200', !isActive);
            });
        };

        const showStep = (step) => {
            currentStep = step;
            stepPanels.forEach((panel) => {
                const isCurrent = Number(panel.dataset.step) === step;
                panel.classList.toggle('hidden', !isCurrent);
            });
            updateIndicators();
            setQuestionNumbers();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };

        const navigateToStep = (targetStep) => {
            if (targetStep === currentStep) return;
            showStep(targetStep);
        };

        const invalidInputClasses = ['border-red-500', 'bg-red-50', 'text-red-900', 'placeholder-red-300', 'focus:border-red-500', 'focus:ring-red-500'];
        const invalidOptionClasses = ['border-red-300', 'bg-red-50', 'text-red-800'];

        const toggleClasses = (elements, classes, enabled) => {
            elements.forEach((element) => {
                classes.forEach((className) => element.classList.toggle(className, enabled));
            });
        };

        const getGroupedInputs = (field) => {
            if (!field.name) return [field];
            return Array.from(form.querySelectorAll(`[name="${CSS.escape(field.name)}"]`)).filter(isEnabledForType);
        };

        const markFieldValidity = (field, isInvalid) => {
            if (field.type === 'radio' || field.type === 'checkbox') {
                const inputs = getGroupedInputs(field);
                const labels = inputs
                    .map((input) => input.closest('label'))
                    .filter(Boolean);
                toggleClasses(labels, invalidOptionClasses, isInvalid);
                return;
            }

            toggleClasses([field], invalidInputClasses, isInvalid);
        };

        const clearInvalidStates = () => {
            Array.from(form.querySelectorAll('input, select, textarea')).forEach((field) => {
                markFieldValidity(field, false);
            });
        };

        const getRequiredFields = () => {
            const fields = Array.from(form.querySelectorAll('input, select, textarea'));
            const uniqueFields = [];
            const groupedNames = [];

            fields.forEach((field) => {
                if (field.disabled || !isEnabledForType(field) || !field.required) return;

                if ((field.type === 'radio' || field.type === 'checkbox') && field.name) {
                    if (groupedNames.includes(field.name)) return;
                    groupedNames.push(field.name);
                }

                uniqueFields.push(field);
            });

            return uniqueFields;
        };

        const validateForm = () => {
            clearInvalidStates();

            const invalidFields = [];
            const requiredFields = getRequiredFields();

            requiredFields.forEach((field) => {
                let isInvalid = false;

                if (field.type === 'radio' || field.type === 'checkbox') {
                    const inputs = getGroupedInputs(field);
                    isInvalid = !inputs.some((input) => input.checked);
                } else {
                    isInvalid = !field.value.trim();
                }

                if (isInvalid) {
                    invalidFields.push(field);
                    markFieldValidity(field, true);
                }
            });

            const conflictRoleInputs = Array.from(form.querySelectorAll('input[name="conflict_roles[]"]')).filter(isEnabledForType);
            const hasConflictRole = conflictRoleInputs.some((input) => input.checked);
            if (!hasConflictRole && conflictRoleInputs.length > 0) {
                invalidFields.push(conflictRoleInputs[0]);
                markFieldValidity(conflictRoleInputs[0], true);
            }

            return invalidFields;
        };

        const getStepForField = (field) => {
            const panel = field.closest('[data-step]');
            return panel ? Number(panel.dataset.step) : 1;
        };

        const bindFieldValidationFeedback = () => {
            Array.from(form.querySelectorAll('input, select, textarea')).forEach((field) => {
                const eventName = field.type === 'radio' || field.type === 'checkbox' ? 'change' : 'input';
                field.addEventListener(eventName, () => {
                    if (field.type === 'radio' || field.type === 'checkbox') {
                        const inputs = getGroupedInputs(field);
                        const hasValue = inputs.some((input) => input.checked);
                        inputs.forEach((input) => markFieldValidity(input, !hasValue && input.required));
                        return;
                    }

                    markFieldValidity(field, field.required && !field.value.trim());
                });
            });
        };

        const nomePfInput = document.getElementById('nome_pf');
        const nomePjHidden = document.getElementById('nome_pj_hidden');
        const razaoSocialInput = document.getElementById('razao_social');

        const setVisibilityAndState = (selector, visible) => {
            form.querySelectorAll(selector).forEach((el) => {
                el.style.display = visible ? '' : 'none';
                if (el.matches('input, select, textarea')) {
                    el.disabled = !visible;
                } else {
                    el.querySelectorAll('input, select, textarea').forEach((field) => {
                        field.disabled = !visible;
                    });
                }
            });
        };

        const toggleRegistrationType = () => {
            const type = getRegistrationType();
            const pj = type === 'pj';

            setVisibilityAndState('.pj-only', pj);
            setVisibilityAndState('.pf-only', !pj);

            if (nomePfInput) nomePfInput.disabled = pj;
            if (nomePjHidden) {
                nomePjHidden.disabled = !pj;
                nomePjHidden.value = pj ? (razaoSocialInput?.value || '') : '';
            }

            if (pj) {
                const cpfInput = document.getElementById('cpf');
                if (cpfInput) cpfInput.value = '';
            }

            setQuestionNumbers();
        };

        if (razaoSocialInput && nomePjHidden) {
            razaoSocialInput.addEventListener('input', () => {
                if (getRegistrationType() === 'pj') {
                    nomePjHidden.value = razaoSocialInput.value || '';
                }
            });
        }

        form.querySelectorAll('input[name="registration_type"]').forEach((r) => r.addEventListener('change', toggleRegistrationType));
        toggleRegistrationType();
        bindFieldValidationFeedback();

        const digits = (value) => value.replace(/\D/g, '');
        const formatCpf = (value) => {
            const v = digits(value).slice(0, 11);
            return v
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        };
        const formatCnpj = (value) => {
            const v = digits(value).slice(0, 14);
            return v
                .replace(/(\d{2})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1/$2')
                .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
        };
        const formatPhone = (value) => {
            const v = digits(value).slice(0, 11);
            if (v.length <= 10) {
                return v
                    .replace(/(\d{2})(\d)/, '($1) $2')
                    .replace(/(\d{4})(\d{1,4})$/, '$1-$2');
            }
            return v
                .replace(/(\d{2})(\d)/, '($1) $2')
                .replace(/(\d{5})(\d{1,4})$/, '$1-$2');
        };
        const attachMask = (id, formatter) => {
            const el = document.getElementById(id);
            if (!el) return;
            const handler = () => { el.value = formatter(el.value); };
            el.addEventListener('input', handler);
            handler();
        };

        const isValidCpf = (value) => {
            const v = digits(value);
            if (v.length !== 11) return false;
            if (/^(\d)\1{10}$/.test(v)) return false;
            const calc = (factor) => {
                let total = 0;
                for (let i = 0; i < factor - 1; i++) {
                    total += parseInt(v[i], 10) * (factor - i);
                }
                const digit = ((total * 10) % 11) % 10;
                return digit === parseInt(v[factor - 1], 10);
            };
            return calc(10) && calc(11);
        };

        const isValidCnpj = (value) => {
            const v = digits(value);
            if (v.length !== 14) return false;
            if (/^(\d)\1{13}$/.test(v)) return false;
            const weights1 = [5,4,3,2,9,8,7,6,5,4,3,2];
            let sum = 0;
            for (let i = 0; i < 12; i++) sum += parseInt(v[i], 10) * weights1[i];
            let rem = sum % 11;
            let check = rem < 2 ? 0 : 11 - rem;
            if (check !== parseInt(v[12], 10)) return false;
            const weights2 = [6,5,4,3,2,9,8,7,6,5,4,3,2];
            sum = 0;
            for (let i = 0; i < 13; i++) sum += parseInt(v[i], 10) * weights2[i];
            rem = sum % 11;
            check = rem < 2 ? 0 : 11 - rem;
            return check === parseInt(v[13], 10);
        };

        attachMask('cpf', formatCpf);
        attachMask('cnpj', formatCnpj);
        attachMask('legal_representative_cpf', formatCpf);
        attachMask('telefone', formatPhone);

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                navigateToStep(2);
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                navigateToStep(1);
            });
        }

        indicators.forEach((indicator) => {
            indicator.addEventListener('click', () => {
                const targetStep = Number(indicator.dataset.goStep);
                if (!targetStep) return;
                navigateToStep(targetStep);
            });
        });

        // Validação imediata ao selecionar arquivos
        const docInput = document.getElementById('documents');
        const docSizeError = document.getElementById('documents-size-error');
        let hasOversizedFiles = false;
        const allowedExtensions = ['pdf','jpg','jpeg','png','doc','docx','zip','rar','7z'];
        if (docInput) {
            docInput.addEventListener('change', () => {
                const maxFileSize = 15 * 1024 * 1024;
                const maxTotalSize = 100 * 1024 * 1024;
                const oversized = [];
                const invalidType = [];
                let totalSize = 0;
                for (let i = 0; i < docInput.files.length; i++) {
                    const file = docInput.files[i];
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (!allowedExtensions.includes(ext)) {
                        invalidType.push(file.name);
                    }
                    if (file.size > maxFileSize) {
                        oversized.push(file.name + ' (' + (file.size / 1024 / 1024).toFixed(1) + 'MB)');
                    }
                    totalSize += file.size;
                }
                const uploadContainer = docInput.closest('.rounded-2xl');
                const errors = [];
                if (oversized.length > 0) {
                    errors.push('⚠ Arquivos acima de 15MB: ' + oversized.join(', ') + '. Reduza o tamanho ou escolha outros arquivos.');
                }
                if (totalSize > maxTotalSize) {
                    errors.push('⚠ Tamanho total (' + (totalSize / 1024 / 1024).toFixed(1) + 'MB) excede o limite de 20MB.');
                }
                if (invalidType.length > 0) {
                    errors.push('⚠ Tipo de arquivo não permitido: ' + invalidType.join(', ') + '. Use PDF, JPG, PNG, DOC, ZIP, RAR ou 7Z.');
                }
                hasOversizedFiles = errors.length > 0;
                if (hasOversizedFiles) {
                    docSizeError.innerHTML = errors.join('<br>');
                    docSizeError.classList.remove('hidden');
                    if (uploadContainer) {
                        uploadContainer.classList.remove('border-slate-200');
                        uploadContainer.classList.add('border-red-500', 'bg-red-50');
                    }
                } else {
                    docSizeError.classList.add('hidden');
                    if (uploadContainer) {
                        uploadContainer.classList.remove('border-red-500', 'bg-red-50');
                        uploadContainer.classList.add('border-slate-200');
                    }
                }
            });
        }

        form.addEventListener('submit', (e) => {
            const invalidFields = validateForm();
            if (invalidFields.length > 0) {
                e.preventDefault();
                showStep(getStepForField(invalidFields[0]));
                invalidFields[0].focus();
                return;
            }

            // Bloquear envio se há erros de arquivo detectados dinamicamente
            if (hasOversizedFiles) {
                e.preventDefault();
                const fileInput = document.getElementById('documents');
                if (fileInput) {
                    showStep(getStepForField(fileInput));
                    fileInput.focus();
                }
                return;
            }

            const type = getRegistrationType();
            const cpfInput = document.getElementById('cpf');
            const cpfError = document.getElementById('cpf-error');
            const cnpjInput = document.getElementById('cnpj');
            const cnpjError = document.getElementById('cnpj-error');
            const legalCpfInput = document.getElementById('legal_representative_cpf');
            const legalCpfError = document.getElementById('legal-cpf-error');

            if (type === 'pf' && cpfInput && cpfInput.value.trim()) {
                if (!isValidCpf(cpfInput.value)) {
                    e.preventDefault();
                    markFieldValidity(cpfInput, true);
                    if (cpfError) cpfError.classList.remove('hidden');
                    showStep(getStepForField(cpfInput));
                    cpfInput.focus();
                    return;
                }
            }
            markFieldValidity(cpfInput, false);
            if (cpfError) cpfError.classList.add('hidden');

            if (type === 'pj' && cnpjInput && cnpjInput.value.trim()) {
                if (!isValidCnpj(cnpjInput.value)) {
                    e.preventDefault();
                    markFieldValidity(cnpjInput, true);
                    if (cnpjError) cnpjError.classList.remove('hidden');
                    showStep(getStepForField(cnpjInput));
                    cnpjInput.focus();
                    return;
                }
            }
            markFieldValidity(cnpjInput, false);
            if (cnpjError) cnpjError.classList.add('hidden');

            if (legalCpfInput && legalCpfInput.value.trim()) {
                if (!isValidCpf(legalCpfInput.value)) {
                    e.preventDefault();
                    markFieldValidity(legalCpfInput, true);
                    if (legalCpfError) legalCpfError.classList.remove('hidden');
                    showStep(getStepForField(legalCpfInput));
                    legalCpfInput.focus();
                    return;
                }
            }
            markFieldValidity(legalCpfInput, false);
            if (legalCpfError) legalCpfError.classList.add('hidden');
        });

        // --- Modal de Termos ---
        const termsPf = @json($terms_pf ?? '');
        const termsPj = @json($terms_pj ?? '');

        const modal = document.getElementById('terms-modal');
        const modalContent = document.getElementById('terms-modal-content');
        const modalBadge = document.getElementById('terms-modal-badge');

        const openModal = () => {
            if (!modal) return;
            const type = getRegistrationType();
            const text = type === 'pj' ? termsPj : termsPf;
            const label = type === 'pj' ? 'Pessoa Jurídica' : 'Pessoa Física';
            if (modalContent) modalContent.textContent = text || 'Nenhum texto de termos configurado.';
            if (modalBadge) modalBadge.textContent = label;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        };

        const closeModal = () => {
            if (!modal) return;
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        };

        document.getElementById('open-terms-modal')?.addEventListener('click', openModal);
        document.getElementById('close-terms-modal')?.addEventListener('click', closeModal);
        document.getElementById('terms-modal-backdrop')?.addEventListener('click', closeModal);
        document.getElementById('accept-terms-btn')?.addEventListener('click', () => {
            const checkbox = document.getElementById('terms_accepted');
            if (checkbox) checkbox.checked = true;
            closeModal();
        });

        // Clicar no checkbox abre a modal em vez de marcar diretamente
        const termsCheckbox = document.getElementById('terms_accepted');
        if (termsCheckbox) {
            termsCheckbox.addEventListener('click', (e) => {
                // Só abre se está tentando marcar (não ao desmarcar)
                if (!termsCheckbox.checked) {
                    e.preventDefault();
                    openModal();
                }
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });

        // Atualiza badge do modal quando tipo muda
        form.querySelectorAll('input[name="registration_type"]').forEach((r) => {
            r.addEventListener('change', () => {
                if (!modal?.classList.contains('hidden')) {
                    openModal();
                }
            });
        });
        // --- fim modal ---

        updateIndicators();
        setQuestionNumbers();
    })();
</script>
@endsection
