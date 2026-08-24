@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 pt-12 pb-10">
    <div class="rounded-3xl bg-gradient-to-r from-red-500 to-rose-500 text-white shadow-xl overflow-hidden mb-8">
        <div class="p-8 md:p-10 space-y-3">
            <p class="text-xs uppercase tracking-[0.25em] text-white/70">Admin • Usuários</p>
            <h1 class="text-3xl md:text-4xl font-black">Criar novo usuário</h1>
            <p class="text-white/80">Cadastre o usuário e configure suas visões e áreas de acesso personalizadas.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
            <p class="font-semibold mb-2">Corrija os campos abaixo:</p>
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass rounded-2xl p-6 shadow-lg border border-white/70">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6" id="user-form">
            @csrf
            
            <div class="space-y-4">
                <h2 class="text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">Informações Pessoais</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-800" for="name">Nome completo</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-800" for="email">E-mail corporativo</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-800" for="password">Senha de acesso</label>
                        <input id="password" name="password" type="password" required
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-800" for="password_confirmation">Confirmar senha</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                    </div>
                </div>
            </div>

            <div class="space-y-4 pt-2">
                <h2 class="text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">Controle de Acesso e Visões</h2>
                
                {{-- Toggle Super Admin --}}
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" id="is_super_admin" name="is_super_admin" value="1" {{ old('is_super_admin') ? 'checked' : '' }}
                            class="mt-1 h-5 w-5 rounded border-slate-300 text-red-600 focus:ring-red-500">
                        <div>
                            <span class="font-bold text-slate-900">Super Administrador (Acesso Total)</span>
                            <p class="text-xs text-slate-500 mt-0.5">Concede acesso total e irrestrito a todas as áreas, gestão de usuários e configurações globais do sistema.</p>
                        </div>
                    </label>
                </div>

                {{-- Seleção de Áreas Específicas --}}
                <div id="areas-selection-container" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-800 mb-2">
                            Áreas / Visões Permitidas <span class="text-xs font-normal text-slate-500">(Selecione uma ou mais)</span>
                        </label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @php 
                                $selectedAreas = old('areas', []); 
                                $availableAreas = $areas->isNotEmpty() ? $areas : collect([
                                    (object)['slug' => 'form-med', 'name' => 'Compliance', 'description' => 'Fornecedores e Documentos'],
                                    (object)['slug' => 'regime-tributario', 'name' => 'Financeiro', 'description' => 'Regime Tributário']
                                ]);
                            @endphp
                            
                            @foreach($availableAreas as $area)
                                <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-white hover:border-red-300 hover:bg-red-50/40 transition cursor-pointer">
                                    <input type="checkbox" name="areas[]" value="{{ $area->slug }}" id="area-{{ $area->slug }}"
                                        {{ in_array($area->slug, $selectedAreas, true) ? 'checked' : '' }}
                                        class="area-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                                    <div>
                                        <span class="font-bold text-slate-800 text-sm">{{ $area->name }}</span>
                                        @if(!empty($area->description))
                                            <p class="text-xs text-slate-500 mt-0.5">{{ $area->description }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Permissões Específicas de Compliance --}}
                    <div id="compliance-permissions-card" class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-800">Classificações permitidas no Compliance</label>
                            <p class="text-xs text-slate-500">Selecione quais submissões este usuário poderá visualizar na área de Compliance:</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            @php $selectedRoles = old('admin_role', ['pj', 'pj-rh', 'pf']); @endphp
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:border-red-200 hover:bg-red-50">
                                <input type="checkbox" name="admin_role[]" value="pj" class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500" {{ in_array('pj', $selectedRoles, true) ? 'checked' : '' }}>
                                <span>PJ Principal (Fornecedores)</span>
                            </label>
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:border-red-200 hover:bg-red-50">
                                <input type="checkbox" name="admin_role[]" value="pj-rh" class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500" {{ in_array('pj-rh', $selectedRoles, true) ? 'checked' : '' }}>
                                <span>PJ RH (Colaboradores)</span>
                            </label>
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:border-red-200 hover:bg-red-50">
                                <input type="checkbox" name="admin_role[]" value="pf" class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500" {{ in_array('pf', $selectedRoles, true) ? 'checked' : '' }}>
                                <span>PF (Pessoa Física)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition">
                    ← Voltar
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold shadow-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    Criar usuário
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const superAdminCheckbox = document.getElementById('is_super_admin');
        const areasContainer = document.getElementById('areas-selection-container');
        const complianceCheckbox = document.getElementById('area-form-med');
        const complianceCard = document.getElementById('compliance-permissions-card');

        function toggleVisibility() {
            if (superAdminCheckbox.checked) {
                areasContainer.style.opacity = '0.4';
                areasContainer.style.pointerEvents = 'none';
            } else {
                areasContainer.style.opacity = '1';
                areasContainer.style.pointerEvents = 'auto';
            }

            if (complianceCheckbox && complianceCard) {
                if (complianceCheckbox.checked && !superAdminCheckbox.checked) {
                    complianceCard.style.display = 'block';
                } else if (!superAdminCheckbox.checked) {
                    complianceCard.style.display = 'none';
                }
            }
        }

        superAdminCheckbox.addEventListener('change', toggleVisibility);
        if (complianceCheckbox) {
            complianceCheckbox.addEventListener('change', toggleVisibility);
        }

        toggleVisibility();
    });
</script>
@endsection