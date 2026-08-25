<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Vitória Hospitalar - Formulários') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(30, 41, 59, 0.8);
        }

        /* Automatic dark mode adaptations for form inputs & containers */
        .dark input[type="text"],
        .dark input[type="email"],
        .dark input[type="password"],
        .dark input[type="number"],
        .dark input[type="tel"],
        .dark input[type="url"],
        .dark input[type="date"],
        .dark select,
        .dark textarea {
            background-color: #1e293b;
            border-color: #334155;
            color: #f8fafc;
        }
        .dark input[type="text"]:focus,
        .dark input[type="email"]:focus,
        .dark select:focus,
        .dark textarea:focus {
            border-color: #ef4444;
            outline: none;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
        }
        .dark input::placeholder,
        .dark textarea::placeholder {
            color: #64748b;
        }
        .dark .step-pill {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #94a3b8 !important;
        }
        .dark .step-pill.is-active,
        .dark .step-pill[data-active="true"] {
            background-color: #dc2626 !important;
            border-color: #ef4444 !important;
            color: #ffffff !important;
        }
    </style>
    <script>
        // Init theme immediately to avoid flash
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="antialiased min-h-full flex flex-col transition-colors duration-200 text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-[#090d16]">
    <header class="sticky top-0 z-30 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
            <a href="{{ route('forms.list') }}" class="flex items-center space-x-3 min-w-0 group">
                <div class="bg-red-50 dark:bg-red-950/50 rounded-xl p-1.5 border border-red-100 dark:border-red-900/50 group-hover:bg-red-100 dark:group-hover:bg-red-900/80 transition shrink-0">
                    <img src="{{ asset('Logo/logo-vitoriahspitalar2.png') }}" alt="Logo" class="h-9 sm:h-10 w-auto object-contain" />
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] sm:text-xs uppercase tracking-[0.2em] text-red-600 dark:text-red-400 font-bold">Vitória Hospitalar</p>
                    <p class="text-sm sm:text-base font-bold text-slate-900 dark:text-white truncate">Portal de Cadastros</p>
                </div>
            </a>
            
            <div class="flex items-center gap-1.5 sm:gap-2">
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('forms.list') }}" class="text-sm text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium px-3 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Formulários
                    </a>
                    <a href="{{ route('compliance.index') }}" class="text-sm text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium px-3 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Documentos Institucionais
                    </a>
                </nav>

                {{-- Dark Mode Toggle Button --}}
                <button
                    type="button"
                    onclick="toggleTheme()"
                    title="Alternar Modo Claro / Escuro"
                    class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition shadow-sm"
                >
                    {{-- Sun icon (visible in dark mode) --}}
                    <svg class="w-4 h-4 hidden dark:block text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{-- Moon icon (visible in light mode) --}}
                    <svg class="w-4 h-4 block dark:hidden text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                @auth
                    <a href="/filament" class="text-xs sm:text-sm text-white font-semibold px-3.5 py-2 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 transition shadow-sm inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Painel Admin</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs sm:text-sm text-white font-semibold px-3.5 py-2 rounded-xl bg-red-600 hover:bg-red-700 transition shadow-sm">
                        Área Admin
                    </a>
                @endauth
            </div>
        </div>
    </header>

    @yield('subnav')
    <main class="flex-1 pb-16">@yield('content')</main>

    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50 py-6 text-center text-xs text-slate-500 dark:text-slate-400 transition-colors">
        <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>© {{ date('Y') }} Vitória Hospitalar. Todos os direitos reservados.</p>
            <p class="text-slate-400 dark:text-slate-500">Sistema Seguro de Cadastros e Compliance</p>
        </div>
    </footer>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>
</body>
</html>
