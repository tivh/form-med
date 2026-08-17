@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 pt-12 pb-10">
    <div class="rounded-3xl bg-gradient-to-r from-red-500 to-rose-500 text-white shadow-xl overflow-hidden mb-8">
        <div class="p-8 md:p-10 space-y-3">
            <p class="text-xs uppercase tracking-[0.25em] text-white/70">Admin • Usuários</p>
            <h1 class="text-3xl md:text-4xl font-black">Criar novo usuário</h1>
            <p class="text-white/80">Defina credenciais de acesso para a área administrativa.</p>
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
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-800" for="name">Nome</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-800" for="email">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-800" for="password">Senha</label>
                    <input id="password" name="password" type="password" required
                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-800" for="password_confirmation">Confirmar senha</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                </div>
            </div>

            @if($isSuperAdmin)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-800" for="form_scope">Área de acesso</label>
                        <select id="form_scope" name="form_scope"
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option value="" {{ old('form_scope') === null ? 'selected' : '' }}>Super Admin (acesso total)</option>
                            @foreach (config('admin_areas') as $slug => $area)
                                <option value="{{ $slug }}" {{ old('form_scope') === $slug ? 'selected' : '' }}>{{ $area['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-800">Classificações permitidas</label>
                        <div class="mt-2 flex flex-wrap gap-3">
                            @php $selectedRoles = old('admin_role', []); @endphp
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm transition hover:border-red-200 hover:bg-red-50">
                                <input type="checkbox" name="admin_role[]" value="pj_diverso" class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500" {{ in_array('pj_diverso', $selectedRoles, true) ? 'checked' : '' }}>
                                <span>PJ normal</span>
                            </label>
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm transition hover:border-red-200 hover:bg-red-50">
                                <input type="checkbox" name="admin_role[]" value="pj_colaborador" class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500" {{ in_array('pj_colaborador', $selectedRoles, true) ? 'checked' : '' }}>
                                <span>PJ RH</span>
                            </label>
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm transition hover:border-red-200 hover:bg-red-50">
                                <input type="checkbox" name="admin_role[]" value="pf" class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500" {{ in_array('pf', $selectedRoles, true) ? 'checked' : '' }}>
                                <span>PF</span>
                            </label>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Selecione uma ou mais classificações que este usuário pode visualizar.</p>
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50">
                    Voltar
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold shadow-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 focus:ring-offset-white">
                    Criar usuário
                </button>
            </div>
        </form>
    </div>
</div>
@endsection