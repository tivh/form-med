@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-red-600 mb-1">Atendimento</p>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Minhas solicitações</h1>
        </div>
        <a href="{{ route('support.create') }}" class="inline-flex items-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition">Nova solicitação</a>
    </div>

    <div class="space-y-4">
        @forelse($requests as $request)
            <a href="{{ route('support.show', $request) }}" class="block glass rounded-2xl border border-white/70 p-5 shadow-lg hover:-translate-y-0.5 transition">
                <div class="flex items-center justify-between gap-3 mb-3 flex-wrap">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">{{ $request->subject }}</h2>
                        <p class="text-xs text-slate-500">Atualizado em {{ $request->updated_at?->format('d/m/Y H:i') }}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                        @if($request->status === 'new') bg-amber-100 text-amber-700 border border-amber-200
                        @elseif($request->status === 'in_progress') bg-sky-100 text-sky-700 border border-sky-200
                        @else bg-emerald-100 text-emerald-700 border border-emerald-200
                        @endif">
                        {{ $request->status === 'new' ? 'Nova' : ($request->status === 'in_progress' ? 'Em andamento' : 'Concluída') }}
                    </span>
                </div>

                @php $lastMessage = $request->messages->last(); @endphp
                @if($lastMessage)
                    <p class="text-sm text-slate-600 line-clamp-3">{{ $lastMessage->message }}</p>
                @endif
            </a>
        @empty
            <div class="glass rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-slate-500">
                Você ainda não enviou nenhuma solicitação.
            </div>
        @endforelse
    </div>
</div>
@endsection
