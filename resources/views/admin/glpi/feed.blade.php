@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto px-4 pt-10 pb-10">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-red-600 mb-1">GLPI</p>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Feed de solicitações rápidas</h1>
            <p class="text-slate-500 text-sm">Atualiza automaticamente a cada 15 segundos.</p>
        </div>
        <div class="flex items-center gap-3">
            <span id="feed-updated-at" class="text-xs text-slate-500"></span>
            <span id="feed-status-dot" class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
        </div>
    </div>

    <div id="feed-error" class="hidden mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 text-sm"></div>

    <div id="feed-empty" class="hidden glass rounded-2xl p-10 text-center border border-white/70 shadow-lg text-slate-500">
        Nenhuma solicitação em aberto no momento.
    </div>

    <div id="feed-list" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
</div>

<script>
    (function () {
        const dataUrl = @json(route('admin.glpi-feed.data'));
        const list = document.getElementById('feed-list');
        const empty = document.getElementById('feed-empty');
        const errorBox = document.getElementById('feed-error');
        const updatedAt = document.getElementById('feed-updated-at');
        const statusDot = document.getElementById('feed-status-dot');
        const POLL_INTERVAL_MS = 15000;

        const statusColors = {
            1: 'bg-red-100 text-red-700 border-red-200',
            2: 'bg-amber-100 text-amber-700 border-amber-200',
            3: 'bg-amber-100 text-amber-700 border-amber-200',
            4: 'bg-slate-100 text-slate-600 border-slate-200',
        };

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value ?? '';
            return div.innerHTML;
        }

        function renderTickets(tickets) {
            list.innerHTML = '';

            if (!tickets.length) {
                empty.classList.remove('hidden');
                return;
            }

            empty.classList.add('hidden');

            tickets.forEach((ticket) => {
                const card = document.createElement('div');
                card.className = 'glass rounded-2xl p-5 border border-white/70 shadow-lg';

                const badgeClass = statusColors[ticket.status] || 'bg-slate-100 text-slate-600 border-slate-200';

                card.innerHTML = `
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border ${badgeClass}">${escapeHtml(ticket.status_label)}</span>
                        <span class="text-xs text-slate-400">#${escapeHtml(ticket.id)}</span>
                    </div>
                    <h2 class="text-base font-bold text-slate-900 mb-2">${escapeHtml(ticket.title)}</h2>
                    <div class="text-xs text-slate-500 space-y-1 mb-4">
                        <p><span class="font-semibold text-slate-700">Categoria:</span> ${escapeHtml(ticket.category)}</p>
                        <p><span class="font-semibold text-slate-700">Solicitante:</span> ${escapeHtml(ticket.requester)}</p>
                        <p><span class="font-semibold text-slate-700">Data:</span> ${escapeHtml(ticket.date)}</p>
                    </div>
                    ${ticket.url ? `<a href="${ticket.url}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 text-xs font-medium">Abrir no GLPI</a>` : ''}
                `;

                list.appendChild(card);
            });
        }

        async function loadFeed() {
            try {
                const response = await fetch(dataUrl, { headers: { Accept: 'application/json' } });
                const payload = await response.json();

                if (!payload.ok) {
                    errorBox.textContent = payload.error || 'Não foi possível carregar o feed do GLPI.';
                    errorBox.classList.remove('hidden');
                    statusDot.className = 'h-2.5 w-2.5 rounded-full bg-red-500';
                    return;
                }

                errorBox.classList.add('hidden');
                statusDot.className = 'h-2.5 w-2.5 rounded-full bg-emerald-500';
                renderTickets(payload.tickets || []);
                updatedAt.textContent = 'Atualizado às ' + new Date().toLocaleTimeString('pt-BR');
            } catch (e) {
                errorBox.textContent = 'Falha de rede ao consultar o feed do GLPI.';
                errorBox.classList.remove('hidden');
                statusDot.className = 'h-2.5 w-2.5 rounded-full bg-red-500';
            }
        }

        loadFeed();
        setInterval(loadFeed, POLL_INTERVAL_MS);
    })();
</script>
@endsection
