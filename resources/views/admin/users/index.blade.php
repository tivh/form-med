@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 pt-12 pb-10 space-y-6">
    <div class="rounded-3xl bg-gradient-to-r from-red-500 to-rose-500 text-white shadow-xl overflow-hidden">
        <div class="p-8 md:p-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div class="space-y-2">
                <p class="text-xs uppercase tracking-[0.25em] text-white/70">Admin • Usuários</p>
                <h1 class="text-3xl md:text-4xl font-black">Usuários cadastrados</h1>
                <p class="text-white/80">Gerencie os acessos da área administrativa.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-red-700 font-semibold shadow-lg hover:bg-slate-50 transition">
                Novo usuário
            </a>
        </div>
    </div>

    <div class="glass rounded-2xl p-6 shadow-lg border border-white/70">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="text-left text-sm font-semibold text-slate-700">
                        <th class="px-4 py-3">Nome</th>
                        <th class="px-4 py-3">E-mail</th>
                        <th class="px-4 py-3">Criado em</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="text-sm text-slate-800">
                            <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ optional($user->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-slate-500">Nenhum usuário cadastrado ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
