@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 pt-12 pb-10">
    <div class="glass rounded-3xl p-10 text-center space-y-4">
        <div class="mx-auto h-16 w-16 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 shadow-inner">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L9 9.586 7.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="space-y-2">
            <p class="text-xs uppercase tracking-[0.2em] text-amber-600">Em preparação</p>
            <h1 class="text-3xl font-bold text-slate-900">Este formulário estará disponível em breve</h1>
            <p class="text-slate-600">Assim que o conteúdo for definido, ele aparecerá automaticamente aqui.</p>
        </div>
    </div>
</div>
@endsection
