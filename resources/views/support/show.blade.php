@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-red-600 mb-1">Solicitação</p>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">{{ $supportRequest->subject }}</h1>
        </div>
        <a href="{{ route('support.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Voltar</a>
    </div>

    <div class="glass rounded-3xl border border-white/70 p-6 shadow-lg">
        <div class="mb-6 border-b border-slate-200 pb-4 flex items-center justify-between gap-3 flex-wrap">
            <div>
                <p class="text-sm text-slate-500">Status atual</p>
                <p class="font-semibold text-slate-800">{{ $supportRequest->status === 'new' ? 'Nova' : ($supportRequest->status === 'in_progress' ? 'Em andamento' : 'Concluída') }}</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                @if($supportRequest->status === 'new') bg-amber-100 text-amber-700 border border-amber-200
                @elseif($supportRequest->status === 'in_progress') bg-sky-100 text-sky-700 border border-sky-200
                @else bg-emerald-100 text-emerald-700 border border-emerald-200
                @endif">
                {{ $supportRequest->status === 'new' ? 'Nova' : ($supportRequest->status === 'in_progress' ? 'Em andamento' : 'Concluída') }}
            </span>
        </div>

        <div class="space-y-4">
            @foreach($supportRequest->messages as $message)
                <div class="rounded-2xl border {{ $message->sender_type === 'admin' ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200' }} p-4">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.18em] {{ $message->sender_type === 'admin' ? 'text-slate-300' : 'text-slate-400' }}">
                            {{ $message->sender_type === 'admin' ? 'Atendimento' : 'Você' }}
                        </span>
                        <span class="text-[11px] {{ $message->sender_type === 'admin' ? 'text-slate-300' : 'text-slate-400' }}">
                            {{ $message->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <p class="whitespace-pre-wrap leading-relaxed">{{ $message->message }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
