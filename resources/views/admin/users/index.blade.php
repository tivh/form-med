@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 pt-12 pb-10 space-y-6">
    <div class="rounded-3xl bg-gradient-to-r from-red-500 to-rose-500 text-white shadow-xl overflow-hidden">
        <div class="p-8 md:p-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="space-y-2">
                <p class="text-xs uppercase tracking-[0.25em] text-white/70">Admin • Usuários</p>
                <h1 class="text-3xl md:text-4xl font-black">Usuários cadastrados</h1>
                <p class="text-white/80">
                    {{ $isSuperAdmin ? 'Gerencie os acessos de todas as áreas.' : 'Gerencie os acessos da sua área.' }}
                </p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-red-700 font-semibold shadow-lg hover:bg-slate-50 transition">
                Novo usuário
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="glass rounded-2xl p-6 shadow-lg border border-white/70">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="text-left text-sm font-semibold text-slate-700">
                        <th class="px-4 py-3">Nome</th>
                        <th class="px-4 py-3">E-mail</th>
                        @if($isSuperAdmin)
                            <th class="px-4 py-3">Área</th>
                        @endif
                        <th class="px-4 py-3">Criado em</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                {{-- dentro de @forelse ($users as $user), acrescentar coluna de ações --}}
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="text-sm text-slate-800">
                            <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            @if($isSuperAdmin)
                                <td class="px-4 py-3">
                                    @php $scopeLabel = config("admin_areas.{$user->form_scope}.label", 'Super Admin'); @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $user->form_scope ? 'bg-slate-100 text-slate-700' : 'bg-red-50 text-red-700 border border-red-100' }}">
                                        {{ $scopeLabel }}
                                    </span>
                                </td>
                            @endif
                            <td class="px-4 py-3">{{ optional($user->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isSuperAdmin ? 5 : 4 }}" class="px-4 py-6 text-center text-slate-500">Nenhum usuário cadastrado ainda.</td>
                        </tr>
                    @endforelse
                </tbody>        
            </table>
        </div>
        <div class="mt-6">{{ $users->links() }}</div>
    </div>
</div>
@endsection