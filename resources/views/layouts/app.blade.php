<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Form Med') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 20px 70px rgba(31, 41, 55, 0.12);
        }
    </style>
</head>
<body class="antialiased text-slate-900 bg-gradient-to-br from-red-50 via-white to-rose-50 min-h-screen">
    <header class="sticky top-0 z-10 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
            <a href="{{ route('forms.list') }}" class="flex items-center space-x-3 min-w-0 group">
                <div class="bg-red-50 rounded-xl p-1.5 border border-red-100 group-hover:bg-red-100 transition shrink-0">
                    <img src="{{ asset('Logo/logo-vitoriahspitalar2.png') }}" alt="Logo" class="h-10 w-auto object-contain" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400 font-medium">Vitória Hospitalar</p>
                    <p class="text-base font-bold text-slate-900 truncate">{{ config('app.name', 'Form Med') }}</p>
                </div>
            </a>
            <nav class="flex items-center gap-1">
                <a href="{{ route('forms.list') }}" class="text-sm text-slate-600 hover:text-slate-900 font-medium px-3 py-1.5 rounded-lg hover:bg-slate-100 transition">Formulários</a>
                <a href="{{ route('login') }}" class="text-sm text-white font-semibold px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 transition ml-2">Área Admin</a>
            </nav>
        </div>
    </header>

    @yield('subnav')
    <main class="pb-16">@yield('content')</main>
</body>
</html>
