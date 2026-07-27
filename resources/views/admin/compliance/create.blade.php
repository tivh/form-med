@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto px-4 pt-12 pb-10">
    <div class="rounded-3xl bg-gradient-to-r from-red-500 to-rose-500 text-white shadow-xl overflow-hidden mb-8">
        <div class="p-8 md:p-10 space-y-3">
            <p class="text-xs uppercase tracking-[0.25em] text-white/70">Admin • Compliance</p>
            <h1 class="text-3xl md:text-4xl font-black">Novo documento</h1>
            <p class="text-white/80">Preencha os dados e anexe o arquivo do documento.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
            <p class="font-semibold mb-2">Corrija os campos abaixo:</p>
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="glass rounded-2xl p-6 shadow-lg border border-white/70">
        <form method="POST" action="{{ route('admin.compliance.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-800" for="title">Título <span class="text-red-500">*</span></label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" required
                    placeholder="Ex.: Política Anticorrupção"
                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-800" for="description">Descrição</label>
                <textarea id="description" name="description" rows="3"
                    placeholder="Breve descrição do conteúdo e finalidade do documento."
                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-800" for="category">Categoria</label>
                    <input id="category" name="category" type="text" value="{{ old('category') }}"
                        placeholder="Ex.: Políticas, Procedimentos"
                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-800" for="sort_order">Ordem de exibição</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}"
                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 shadow-sm focus:border-red-500 focus:ring-red-500" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-800" for="file">Arquivo <span class="text-red-500">*</span></label>
                <p class="text-xs text-slate-500 mt-0.5 mb-2">PDF, Word, Excel, PowerPoint, TXT ou ZIP — máx. 50 MB</p>
                <input id="file" name="file" type="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip"
                    class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer" />
            </div>

            <div class="flex items-center gap-3 pt-1">
                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500" />
                <label for="is_active" class="text-sm font-medium text-slate-800">Documento visível na página pública</label>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <a href="{{ route('admin.compliance.index') }}" class="text-sm text-slate-500 hover:text-slate-800 font-medium transition">← Voltar</a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-red-600 text-white font-semibold shadow hover:bg-red-700 hover:-translate-y-0.5 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Salvar documento
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
