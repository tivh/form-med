@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-red-600 mb-1">Solicitação</p>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">{{ $supportRequest->subject }}</h1>
        </div>
        <a href="{{ route('admin.support.feed') }}" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Voltar ao feed</a>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="glass rounded-3xl border border-white/70 p-6 shadow-lg">
        <div class="mb-6 border-b border-slate-200 pb-4 flex items-center justify-between gap-3 flex-wrap">
            <div>
                <p class="text-sm text-slate-500">Solicitante: <span class="font-semibold text-slate-800">{{ $supportRequest->requester_name }}</span></p>
                <p class="text-sm text-slate-500">Email: <span class="font-semibold text-slate-800">{{ $supportRequest->requester_email }}</span></p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                @if($supportRequest->status === 'new') bg-amber-100 text-amber-700 border border-amber-200
                @elseif($supportRequest->status === 'in_progress') bg-sky-100 text-sky-700 border border-sky-200
                @else bg-emerald-100 text-emerald-700 border border-emerald-200
                @endif">
                {{ $supportRequest->status === 'new' ? 'Novo' : ($supportRequest->status === 'in_progress' ? 'Em andamento' : 'Concluído') }}
            </span>
        </div>

        <div class="space-y-4 mb-8">
            @foreach($supportRequest->messages as $message)
                <div class="rounded-2xl border {{ $message->sender_type === 'admin' ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200' }} p-4">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-[0.18em] {{ $message->sender_type === 'admin' ? 'text-slate-300' : 'text-slate-400' }}">
                            {{ $message->sender_type === 'admin' ? 'Atendimento' : 'Usuário' }}
                        </span>
                        <span class="text-[11px] {{ $message->sender_type === 'admin' ? 'text-slate-300' : 'text-slate-400' }}">
                            {{ $message->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <p class="whitespace-pre-wrap leading-relaxed">{{ $message->message }}</p>
                </div>
            @endforeach
        </div>

        @if($supportRequest->status !== 'closed')
            <form action="{{ route('admin.support.reply', $supportRequest) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="message" class="block text-sm font-medium text-slate-700 mb-2">Responder</label>
                    <textarea id="message" name="message" rows="5" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 focus:border-red-400 focus:ring-2 focus:ring-red-100" placeholder="Escreva a resposta para o usuário..." required></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition">Enviar resposta</button>
                </div>
            </form>

            <form action="{{ route('admin.support.close', $supportRequest) }}" method="POST" class="mt-4">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 transition">Concluir solicitação</button>
            </form>
        @endif
    </div>
</div>
@endsection
