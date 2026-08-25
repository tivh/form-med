@extends('layouts.app')

@section('subnav')
@php
    $user = auth()->user();
    $accessibleAreas = $user ? $user->accessibleAreas() : collect();
    $showHome = $user && ($user->isSuperAdmin() || $accessibleAreas->count() > 1);
@endphp
<nav class="bg-slate-900 border-b border-slate-700 sticky top-[69px] z-10">
    <div class="max-w-6xl mx-auto px-4 flex items-center justify-between">
        <div class="flex items-center overflow-x-auto">
            @if($showHome)
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition
                   {{ request()->routeIs('admin.dashboard') ? 'border-red-400 text-white' : 'border-transparent text-slate-400 hover:text-white hover:border-slate-500' }}">
                    Início
                </a>
            @endif

            @foreach (config('admin_areas') as $slug => $area)
                @continue(!$user || !$user->canAccess($slug))
                @foreach ($area['nav_items'] as $item)
                    <a href="{{ route($item['route']) }}"
                       class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition
                       {{ request()->routeIs($item['pattern']) ? 'border-red-400 text-white' : 'border-transparent text-slate-400 hover:text-white hover:border-slate-500' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            @endforeach

            @if($user && $user->isSuperAdmin())
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition
                   {{ request()->routeIs('admin.users.*') ? 'border-red-400 text-white' : 'border-transparent text-slate-400 hover:text-white hover:border-slate-500' }}">
                    Usuários
                </a>

                <a href="{{ route('admin.settings.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition
                   {{ request()->routeIs('admin.settings.*') ? 'border-red-400 text-white' : 'border-transparent text-slate-400 hover:text-white hover:border-slate-500' }}">
                    Configurações
                </a>
            @endif
        </div>
        <div class="flex items-center gap-3 py-2 shrink-0">
            <a href="/filament" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-red-600 to-rose-600 text-white hover:from-red-700 hover:to-rose-700 transition shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Novo Painel Unificado (Filament)</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="inline-flex items-center">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-700 transition">
                    Sair
                </button>
            </form>
        </div>
    </div>
</nav>
@endsection