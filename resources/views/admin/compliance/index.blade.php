@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 pt-12 pb-10">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-red-500 via-red-400 to-rose-400 text-white shadow-xl mb-8">
        <div class="absolute right-8 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="p-8 md:p-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.25em] text-white/80">Admin • Compliance</p>
                <h1 class="text-3xl md:text-4xl font-black leading-tight">Documentos de Compliance</h1>
                <p class="text-white/80">Gerencie os documentos institucionais e de procedimentos da empresa.</p>
                <div class="flex flex-wrap gap-3 text-sm">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/20">
                        <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>
                        {{ $documents->total() }} documento(s)
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('admin.compliance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/15 text-white font-semibold border border-white/25 shadow hover:bg-white/25 hover:-translate-y-0.5 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                    Novo documento
                </a>

            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 flex items-center gap-3">
            <svg class="h-5 w-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L9 9.586 7.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('status') }}
        </div>
    @endif

    @if($documents->isEmpty())
        <div class="glass rounded-2xl p-12 text-center shadow-sm border border-white/70">
            <div class="mx-auto mb-4 h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center">
                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-slate-600 font-medium">Nenhum documento cadastrado.</p>
            <a href="{{ route('admin.compliance.create') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-red-600 text-white font-semibold shadow hover:bg-red-700 transition text-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Adicionar primeiro documento
            </a>
        </div>
    @else
        <div class="glass rounded-2xl shadow-lg border border-white/70 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80">
                            <th class="text-left px-6 py-3 font-semibold text-slate-700">Título</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-700 hidden md:table-cell">Categoria</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-700 hidden lg:table-cell">Arquivo</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-700 hidden sm:table-cell">Status</th>
                            <th class="text-left px-4 py-3 font-semibold text-slate-700 hidden lg:table-cell">Ordem</th>
                            <th class="text-right px-6 py-3 font-semibold text-slate-700">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($documents as $doc)
                            <tr class="hover:bg-red-50/40 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-900">{{ $doc->title }}</p>
                                    @if($doc->description)
                                        <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $doc->description }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4 hidden md:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ $doc->category ?: 'Geral' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 hidden lg:table-cell">
                                    <span class="text-xs text-slate-500 font-mono">{{ $doc->file_original_name }}</span>
                                    <span class="ml-1 text-xs text-slate-400">({{ $doc->formattedFileSize() }})</span>
                                </td>
                                <td class="px-4 py-4 hidden sm:table-cell">
                                    @if($doc->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Ativo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 hidden lg:table-cell text-slate-500 text-xs">{{ $doc->sort_order }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.compliance.download', $doc) }}" title="Baixar" class="inline-flex items-center p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                        <a href="{{ route('admin.compliance.edit', $doc) }}" title="Editar" class="inline-flex items-center p-2 rounded-lg text-slate-500 hover:bg-blue-50 hover:text-blue-700 transition">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.compliance.destroy', $doc) }}" method="POST" onsubmit="return confirm('Excluir este documento? Essa ação não pode ser desfeita.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Excluir" class="inline-flex items-center p-2 rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-700 transition">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($documents->hasPages())
            <div class="mt-6">{{ $documents->links() }}</div>
        @endif
    @endif
</div>
@endsection
