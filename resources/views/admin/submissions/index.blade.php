@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 pt-12 pb-10">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-red-500 via-red-400 to-rose-400 text-white shadow-xl mb-8">
        <div class="absolute right-8 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="p-8 md:p-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.25em] text-white/80">Admin • Monitoramento</p>
                <h1 class="text-3xl md:text-4xl font-black leading-tight">Submissões recebidas</h1>
                <p class="text-white/80">Acompanhe, filtre, exporte e revise cada formulário enviado.</p>
                <div class="flex flex-wrap gap-3 text-sm">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/20">
                        <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>
                        {{ $submissions->total() }} itens
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/20">
                        Atualiza em tempo real
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/20">
                        Documentos guardados por 15 dias
                    </span>
                </div>
            </div>

        </div>
    </div>

    <form method="GET" action="{{ route('admin.submissions.index') }}" class="glass rounded-2xl p-6 mb-6 grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-4 shadow-lg">
        <div class="md:col-span-2 lg:col-span-2">
            <label class="block text-sm font-semibold text-slate-800">Nome / Razão social</label>
            <input type="text" name="name" value="{{ request('name') }}" class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Nome ou razão social">
        </div>
        <div class="md:col-span-2 lg:col-span-2">
            <label class="block text-sm font-semibold text-slate-800">E-mail</label>
            <input type="email" name="email" value="{{ request('email') }}" class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="email@dominio.com">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-800">Tipo</label>
            <select name="registration_type" class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 shadow-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Todos</option>
                <option value="pj" {{ request('registration_type') === 'pj' ? 'selected' : '' }}>Pessoa Jurídica</option>
                <option value="pf" {{ request('registration_type') === 'pf' ? 'selected' : '' }}>Pessoa Física</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-800">Formulário</label>
            <select name="form_type" class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 shadow-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Todos</option>
                @foreach($formCatalog as $slug => $form)
                    <option value="{{ $slug }}" {{ request('form_type') === $slug ? 'selected' : '' }}>{{ $form['title'] ?? $slug }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-800">Data de</label>
            <input type="date" name="from" value="{{ request('from') }}" class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 shadow-sm focus:border-red-500 focus:ring-red-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-800">Data até</label>
            <input type="date" name="to" value="{{ request('to') }}" class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 shadow-sm focus:border-red-500 focus:ring-red-500">
        </div>
        <div class="md:col-span-4 lg:col-span-6 flex flex-wrap items-center gap-3 pt-2 border-t border-slate-100 mt-2">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 text-white font-semibold shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1016.65 16.65z"/></svg>
                Filtrar
            </button>
            <a href="{{ route('admin.submissions.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50">Limpar</a>
        </div>
    </form>

    @if(!empty($status))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 p-4">
            {{ $status }}
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between mb-4 gap-3 text-sm text-slate-700">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 border border-slate-200">
            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5h18M3 12h18M3 19h18"/></svg>
            <span>{{ $submissions->total() }} resultados</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.submissions.export', array_merge(request()->only(['email','name','registration_type','from','to','form_type']), ['format' => 'csv'])) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 10l5 5m0 0l5-5m-5 5V3"/></svg>
                Exportar CSV
            </a>
            <a href="{{ route('admin.submissions.export', array_merge(request()->only(['email','name','registration_type','from','to','form_type']), ['format' => 'xlsx'])) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17l-2 2m0 0l-2-2m2 2V7m11 10l-2 2m0 0l-2-2m2 2V7"/></svg>
                Exportar XLSX
            </a>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($submissions as $submission)
            <div class="glass rounded-2xl p-6 border border-white/60 shadow-lg hover:shadow-xl transition">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-start">
                    <!-- Formulário -->
                    <div class="lg:col-span-1">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Formulário</p>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200">
                            {{ $formCatalog[$submission->form_type]['title'] ?? $submission->form_type ?? '—' }}
                        </span>
                    </div>

                    <!-- Nome / Razão Social -->
                    <div class="lg:col-span-1">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Nome</p>
                        <p class="text-sm font-semibold text-slate-900">
                            {{ $submission->registration_type === 'pj' ? ($submission->razao_social ?? $submission->nome) : $submission->nome }}
                        </p>
                        <p class="text-xs text-slate-500 mt-1">{{ $submission->email }}</p>
                    </div>

                    <!-- Tipo -->
                    <div class="lg:col-span-1">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Tipo</p>
                        @php $isPj = $submission->registration_type === 'pj'; @endphp
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold {{ $isPj ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' }}">
                            <span class="h-2 w-2 rounded-full {{ $isPj ? 'bg-red-500' : 'bg-emerald-500' }}"></span>
                            {{ $isPj ? 'PJ' : 'PF' }}
                        </span>
                    </div>

                    <!-- Verificado -->
                    <div class="lg:col-span-1">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Verificado</p>
                        <form method="POST" action="{{ route('admin.submissions.toggle-verified', $submission) }}" class="inline">
                            @csrf
                            <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border font-semibold cursor-pointer transition {{ $submission->verified ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' }}">
                                <input type="checkbox" name="verified" value="1" {{ $submission->verified ? 'checked' : '' }} class="h-4 w-4 rounded cursor-pointer" onchange="this.form.submit()">
                                <span class="text-xs font-semibold">{{ $submission->verified ? '✓ Verificado' : '✗ Não verificado' }}</span>
                            </label>
                        </form>
                    </div>

                    <!-- Data -->
                    <div class="lg:col-span-1">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Data</p>
                        <p class="text-sm font-semibold text-slate-600">{{ optional($submission->created_at)->format('d/m/Y') }}</p>
                        <p class="text-xs text-slate-500">{{ optional($submission->created_at)->format('H:i') }}</p>
                    </div>

                    <!-- Ações -->
                    <div class="lg:col-span-1">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500 font-semibold mb-2">Ações</p>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.submissions.show', $submission) }}" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 text-xs font-medium">Ver</a>
                            @if(is_array($submission->documents) && count($submission->documents))
                                <a href="{{ route('admin.submissions.download', $submission) }}" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100 text-xs font-medium">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download
                                </a>
                            @endif
                            <form method="POST" action="{{ route('admin.submissions.destroy', $submission) }}" class="inline" onsubmit="return confirm('Remover essa submissão?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-100 hover:bg-red-200 text-xs font-medium">Excluir</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-xl bg-slate-50 border border-slate-200">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m0 6a9 9 0 100-18 9 9 0 000 18z"/></svg>
                    <span class="text-slate-600 font-medium">Nenhuma submissão encontrada.</span>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4 text-slate-800">{{ $submissions->withQueryString()->links() }}</div>
</div>
@endsection
