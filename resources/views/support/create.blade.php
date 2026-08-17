@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 pt-10">
    <div class="glass rounded-3xl p-8 border border-white/70 shadow-lg">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.2em] text-red-600 mb-2">Atendimento</p>
            <h1 class="text-3xl font-bold text-slate-900">Solicitar ajuda</h1>
            <p class="text-slate-500 mt-2">Descreva sua necessidade e o suporte vai responder por aqui.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('support.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="subject" class="block text-sm font-medium text-slate-700 mb-2">Assunto</label>
                <input id="subject" name="subject" type="text" value="{{ old('subject') }}" placeholder="Ex: Preciso de uma licença" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-red-400 focus:ring-2 focus:ring-red-100" required>
                @error('subject')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-slate-700 mb-2">Mensagem</label>
                <textarea id="message" name="message" rows="6" placeholder="Conte o que você precisa..." class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-red-400 focus:ring-2 focus:ring-red-100" required>{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between gap-3 pt-2">
                <a href="{{ route('forms.list') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Voltar</a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition">Enviar solicitação</button>
            </div>
        </form>
    </div>
</div>
@endsection
