@extends('layouts.app')

@section('subnav')
<nav class="bg-slate-900 border-b border-slate-700 sticky top-[69px] z-10">
    <div class="max-w-6xl mx-auto px-4 flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('admin.submissions.index') }}"
               class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition
               {{ request()->routeIs('admin.submissions.*') ? 'border-red-400 text-white' : 'border-transparent text-slate-400 hover:text-white hover:border-slate-500' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Submissões
            </a>
            <a href="{{ route('admin.compliance.index') }}"
               class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition
               {{ request()->routeIs('admin.compliance.*') ? 'border-red-400 text-white' : 'border-transparent text-slate-400 hover:text-white hover:border-slate-500' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Documentos
            </a>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition
               {{ request()->routeIs('admin.users.*') ? 'border-red-400 text-white' : 'border-transparent text-slate-400 hover:text-white hover:border-slate-500' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Usuários
            </a>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="flex items-center py-2">
            @csrf
            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-700 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                Sair
            </button>
        </form>
    </div>
</nav>
@endsection
