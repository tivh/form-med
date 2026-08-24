@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 pt-12 pb-10">
    <div class="rounded-3xl bg-gradient-to-r from-red-500 to-rose-500 text-white shadow-xl overflow-hidden mb-8 p-8">
        <p class="text-xs uppercase tracking-[0.25em] text-white/70">
            Admin • {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Painel de Controle' }}
        </p>
        <h1 class="text-3xl md:text-4xl font-black">Painel geral</h1>
        <p class="text-white/80">
            {{ auth()->user()->isSuperAdmin() ? 'Gerencie todas as áreas e acessos do sistema.' : 'Selecione uma área abaixo para acessar.' }}
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($areas as $area)
            <a href="{{ route($area['route']) }}" class="glass rounded-2xl p-6 shadow-lg border border-white/70 hover:-translate-y-1 transition">
                <p class="text-xs uppercase tracking-[0.2em] text-red-600 font-semibold mb-2">{{ $area['label'] }}</p>
                <h2 class="text-xl font-bold text-slate-900 mb-1">{{ $area['title'] }}</h2>
                <p class="text-slate-500 text-sm">{{ $area['count'] }} registro(s) recebido(s)</p>
            </a>
        @endforeach

        @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.users.index') }}" class="glass rounded-2xl p-6 shadow-lg border border-white/70 hover:-translate-y-1 transition md:col-span-2">
                <p class="text-xs uppercase tracking-[0.2em] text-red-600 font-semibold mb-2">Acessos</p>
                <h2 class="text-xl font-bold text-slate-900 mb-1">Usuários</h2>
                <p class="text-slate-500 text-sm">Gerencie permissões e visões de acesso dos usuários</p>
            </a>
        @endif
    </div>
</div>
@endsection