@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 pt-10 pb-16">

    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-red-600 via-red-500 to-rose-600 text-white shadow-xl mb-10">
        <div class="absolute right-0 top-0 w-80 h-80 bg-white/10 rounded-full -translate-y-1/3 translate-x-1/3 blur-2xl pointer-events-none"></div>
        <div class="absolute left-1/2 bottom-0 w-60 h-60 bg-rose-400/20 rounded-full translate-y-1/2 blur-2xl pointer-events-none"></div>
        <div class="relative p-8 md:p-12 space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-xs font-semibold uppercase tracking-widest text-white/90">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Documentação Corporativa
            </div>
            <h1 class="text-3xl md:text-5xl font-extrabold leading-tight">Documentos de Compliance</h1>
            <p class="text-white/90 max-w-xl text-base md:text-lg">Acesse os documentos institucionais, políticas e procedimentos da empresa. Clique em qualquer item para ver os detalhes ou efetuar o download.</p>
        </div>
    </div>

    @if($documentsByCategory->isEmpty())
        <div class="glass rounded-2xl p-12 text-center shadow-sm">
            <div class="mx-auto mb-4 h-16 w-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-slate-700 dark:text-slate-300 font-medium">Nenhum documento disponível no momento.</p>
            <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">Em breve novos documentos serão publicados aqui.</p>
        </div>
    @else
        @foreach($documentsByCategory as $category => $documents)
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-red-600 dark:text-red-400 px-2">
                        {{ $category ?: 'Geral' }}
                    </span>
                    <div class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></div>
                </div>

                <div class="space-y-3">
                    @foreach($documents as $doc)
                        <div class="group rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900/90 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                            {{-- Collapsed header --}}
                            <button
                                type="button"
                                onclick="toggleDoc('doc-{{ $doc->id }}')"
                                class="w-full flex items-center gap-4 p-5 text-left hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors"
                            >
                                {{-- File icon --}}
                                <div class="flex-shrink-0 h-11 w-11 rounded-xl bg-red-50 dark:bg-red-950/60 border border-red-100 dark:border-red-900/50 flex items-center justify-center">
                                    @php
                                        $ext = strtolower(pathinfo($doc->file_original_name, PATHINFO_EXTENSION));
                                        $extColors = [
                                            'pdf'  => 'text-red-600 dark:text-red-400',
                                            'doc'  => 'text-blue-600 dark:text-blue-400',
                                            'docx' => 'text-blue-600 dark:text-blue-400',
                                            'xls'  => 'text-emerald-600 dark:text-emerald-400',
                                            'xlsx' => 'text-emerald-600 dark:text-emerald-400',
                                            'ppt'  => 'text-orange-600 dark:text-orange-400',
                                            'pptx' => 'text-orange-600 dark:text-orange-400',
                                        ];
                                        $iconColor = $extColors[$ext] ?? 'text-slate-500';
                                    @endphp
                                    <svg class="h-5 w-5 {{ $iconColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-900 dark:text-white truncate">{{ $doc->title }}</p>
                                    @if($doc->description)
                                        <p class="text-sm text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ $doc->description }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="hidden sm:inline text-xs text-slate-400 dark:text-slate-500 font-mono">{{ strtoupper($ext) }} · {{ $doc->formattedFileSize() }}</span>
                                    <svg id="chevron-doc-{{ $doc->id }}" class="h-5 w-5 text-slate-400 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </button>

                            {{-- Expanded detail --}}
                            <div id="doc-{{ $doc->id }}" class="hidden border-t border-slate-100 dark:border-slate-800 px-5 py-5 bg-slate-50/50 dark:bg-slate-900/50">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                    <div class="space-y-2 flex-1">
                                        @if($doc->description)
                                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ $doc->description }}</p>
                                        @endif
                                        <div class="flex flex-wrap gap-3 text-xs text-slate-500 dark:text-slate-400">
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                {{ $doc->file_original_name }}
                                            </span>
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                                {{ $doc->formattedFileSize() }}
                                            </span>
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                Publicado em {{ $doc->created_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                    <a
                                        href="{{ route('compliance.download', $doc) }}"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-500 text-white text-sm font-semibold shadow-sm hover:shadow transition-all flex-shrink-0"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Baixar documento
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>

<script>
function toggleDoc(id) {
    const panel   = document.getElementById(id);
    const chevron = document.getElementById('chevron-' + id);
    if (!panel) return;
    const isOpen = !panel.classList.contains('hidden');
    panel.classList.toggle('hidden', isOpen);
    chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
}
</script>
@endsection
