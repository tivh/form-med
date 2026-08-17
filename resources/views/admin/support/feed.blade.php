@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-red-600 mb-1">Atendimento</p>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Feed de solicitações</h1>
        </div>
        <span class="inline-flex items-center rounded-full bg-red-50 border border-red-100 px-3 py-1.5 text-xs font-semibold text-red-700">
            {{ $requests->count() }} em fila
        </span>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($requests as $request)
            <div class="glass rounded-2xl p-5 border border-white/70 shadow-lg">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                        @if($request->status === 'new') bg-amber-100 text-amber-700 border border-amber-200
                        @elseif($request->status === 'in_progress') bg-sky-100 text-sky-700 border border-sky-200
                        @else bg-emerald-100 text-emerald-700 border border-emerald-200
                        @endif">
                        {{ $request->status === 'new' ? 'Novo' : ($request->status === 'in_progress' ? 'Em andamento' : 'Concluído') }}
                    </span>
                    <span class="text-xs text-slate-400">#{{ $request->id }}</span>
                </div>

                <h2 class="text-base font-bold text-slate-900 mb-2">{{ $request->subject }}</h2>

                <div class="text-xs text-slate-500 space-y-1 mb-4">
                    <p><span class="font-semibold text-slate-700">Solicitante:</span> {{ $request->requester_name }}</p>
                    <p><span class="font-semibold text-slate-700">Email:</span> {{ $request->requester_email }}</p>
                    <p><span class="font-semibold text-slate-700">Fonte:</span> {{ $request->source }}</p>
                    <p><span class="font-semibold text-slate-700">Última atualização:</span> {{ $request->updated_at?->format('d/m/Y H:i') }}</p>
                </div>

                @php $lastMessage = $request->messages->last(); @endphp
                @if($lastMessage)
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-sm text-slate-700 mb-4">
                        <div class="text-[10px] uppercase tracking-[0.18em] text-slate-400 mb-1">Última mensagem</div>
                        <p class="line-clamp-4">{{ $lastMessage->message }}</p>
                    </div>
                @endif

                <div class="flex gap-2 flex-wrap">
                    <a href="{{ route('admin.support.show', $request) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-700 transition">Abrir</a>
                    @if($request->status !== 'closed')
                        <a href="{{ route('admin.support.show', $request) }}#responder" class="inline-flex items-center px-3 py-2 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition">Responder</a>
                        <form action="{{ route('admin.support.close', $request) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center px-3 py-2 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-100 transition">Concluir</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="md:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-slate-500">
                Nenhuma solicitação cadastrada no momento.
            </div>
        @endforelse
    </div>
</div>
@endsection
