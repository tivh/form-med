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
        <form action="{{ route('logout') }}" method="POST" class="flex items-center py-2 shrink-0">
            @csrf
            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-700 transition">
                Sair
            </button>
        </form>
    </div>
</nav>
@endsection