@extends('layouts.app')

@section('content')
<style>
    body {
        background: #b91c1c !important;
    }
    main {
        padding-bottom: 0 !important;
    }
    .login-hero {
        background: radial-gradient(circle at 20% 20%, rgba(220, 38, 38, 0.5), transparent 50%),
            radial-gradient(circle at 80% 0%, rgba(185, 28, 28, 0.4), transparent 45%),
            linear-gradient(135deg, #b91c1c 0%, #dc2626 50%, #b91c1c 100%);
    }
    .glass-card {
        backdrop-filter: blur(14px);
        background: linear-gradient(145deg, rgba(255,255,255,0.92), rgba(255,255,255,0.86));
        border: 1px solid rgba(226, 232, 240, 0.7);
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.16);
        transition: transform 220ms ease, box-shadow 220ms ease;
    }
    .glass-card:hover { transform: translateY(-4px); box-shadow: 0 30px 90px rgba(15, 23, 42, 0.18); }
    .fade-up { opacity: 0; transform: translateY(12px); transition: opacity 280ms ease, transform 320ms ease; }
    .fade-up.visible { opacity: 1; transform: translateY(0); }
</style>

<div class="relative min-h-[calc(100vh-56px)] flex items-center justify-center pt-14 pb-16 px-4 login-hero overflow-hidden">
    <div class="absolute -left-24 top-10 w-56 h-56 bg-red-500/30 blur-3xl rounded-full"></div>
    <div class="absolute -right-24 bottom-0 w-64 h-64 bg-red-600/25 blur-3xl rounded-full"></div>

    <div class="w-full max-w-md glass-card rounded-3xl p-8 md:p-9 fade-up" id="login-card">
        <div class="flex flex-col items-center mb-7 space-y-2 text-center">
            <div class="bg-slate-200 rounded-xl p-3 shadow-inner border border-slate-300">
                <img src="{{ asset('Logo/logo-vitoriahspitalar2.png') }}" alt="Logo" class="h-16 w-auto object-contain" />
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900">Acesso administrativo</h1>
            <p class="text-sm text-slate-600">Somente usuários autorizados.</p>
        </div>
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('login.perform') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-800" for="email">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-800" for="password">Senha</label>
                <input id="password" name="password" type="password" required
                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-400 shadow-sm focus:border-red-500 focus:ring-red-500" />
            </div>
            <div class="flex items-center justify-between">
                <label class="inline-flex items-center text-sm text-slate-700">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-red-600 shadow-sm focus:border-red-400 focus:ring-red-400">
                    <span class="ml-2">Lembrar</span>
                </label>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-4 py-2 text-sm font-semibold text-white shadow-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 focus:ring-offset-white">
                    Entrar
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7l5 5-5 5M6 12h12"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (() => {
        const card = document.getElementById('login-card');
        if (!card) return;
        requestAnimationFrame(() => card.classList.add('visible'));
    })();
</script>
@endsection
