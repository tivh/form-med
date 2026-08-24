@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 pt-12 pb-10 space-y-6">
    <div class="rounded-3xl bg-gradient-to-r from-red-500 to-rose-500 text-white shadow-xl overflow-hidden">
        <div class="p-8 md:p-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="space-y-2">
                <p class="text-xs uppercase tracking-[0.25em] text-white/70">Admin • Usuários</p>
                <h1 class="text-3xl md:text-4xl font-black">Usuários cadastrados</h1>
                <p class="text-white/80">
                    Gerencie os usuários e configure as visões de acesso personalizadas por área.
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
                        <th class="px-4 py-3">Áreas de Acesso</th>
                        <th class="px-4 py-3">Criado em</th>
                        <th class="px-4 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="text-sm text-slate-800">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1.5 items-center">
                                    @if($user->isSuperAdmin())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                            Super Admin (Acesso Total)
                                        </span>
                                    @elseif($user->areas->isNotEmpty())
                                        @foreach($user->areas as $area)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                                {{ $area->name }}
                                            </span>
                                        @endforeach
                                    @elseif($user->form_scope)
                                        @php $scopeLabel = config("admin_areas.{$user->form_scope}.label", $user->form_scope); @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $scopeLabel }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            Sem áreas
                                        </span>
                                    @endif

                                    @php $allowed = $user->allowedClassifications(); @endphp
                                    @if(!$user->isSuperAdmin() && $user->canAccess('form-med') && $allowed !== [] && count($allowed) < 3)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                            Filtro: {{ implode(', ', array_map('strtoupper', $allowed)) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ optional($user->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100 font-medium transition">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-500">Nenhum usuário cadastrado ainda.</td>
                        </tr>
                    @endforelse
                </tbody>        
            </table>
        </div>
        <div class="mt-6">{{ $users->links() }}</div>
    </div>
</div>
@endsection