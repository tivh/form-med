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
    <header class="sticky top-0 z-10 bg-slate-200 border-b border-slate-300 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="bg-slate-300 rounded-md p-1.5 shadow-inner">
                    <img src="{{ asset('Logo/logo-vitoriahspitalar2.png') }}" alt="Logo" class="h-10 w-auto object-contain" />
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-600">Vitória Hospitalar</p>
                    <p class="text-lg font-semibold text-slate-900">{{ config('app.name', 'Form Med') }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('login') }}" class="text-sm text-red-800 hover:text-red-900 font-semibold border border-red-100 px-4 py-2 rounded-full bg-red-100/80">Área Admin</a>
            </div>
        </div>
    </header>

    <main class="pb-16">@yield('content')</main>
</body>
</html>
