<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GlpiFeedService
{
    private string $apiBaseUrl;
    private ?string $appToken;
    private ?string $userToken;
    private array $allowedStatuses;
    private ?string $categoryFilter;
    private string $frontBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim((string) config('services.glpi.api_base_url'), '/');
        $this->appToken = config('services.glpi.app_token') ?: null;
        $this->userToken = config('services.glpi.user_token') ?: null;
        $this->frontBaseUrl = rtrim((string) config('services.glpi.front_base_url'), '/');

        $this->allowedStatuses = array_values(array_filter(array_map(
            fn ($value) => (int) trim($value),
            explode(',', (string) config('services.glpi.feed_statuses', '1,2,3,4'))
        )));

        $categoryFilter = trim((string) config('services.glpi.feed_category_name', ''));
        $this->categoryFilter = $categoryFilter !== '' ? $categoryFilter : null;
    }

    /**
     * Fetch the open tickets for the live feed.
     *
     * @return array{ok: bool, tickets: array<int, array<string, mixed>>, error?: string}
     */
    public function fetchTickets(): array
    {
        if ($this->apiBaseUrl === '' || !$this->userToken) {
            return [
                'ok' => false,
                'tickets' => [],
                'error' => 'Integração com o GLPI não está configurada (verifique GLPI_API_BASE_URL e GLPI_USER_TOKEN no .env).',
            ];
        }

        $sessionToken = null;

        try {
            $sessionToken = $this->initSession();

            $response = $this->client($sessionToken)
                ->get("{$this->apiBaseUrl}/Ticket", [
                    'expand_dropdowns' => true,
                    'range' => '0-100',
                ]);

            if (!$response->successful()) {
                Log::warning('GLPI feed: falha ao consultar tickets', [
                    'status' => $response->status(),
                ]);

                return [
                    'ok' => false,
                    'tickets' => [],
                    'error' => 'Não foi possível consultar os chamados no GLPI (status '.$response->status().').',
                ];
            }

            $tickets = collect($response->json() ?? [])
                ->filter(fn ($ticket) => is_array($ticket))
                ->filter(fn ($ticket) => in_array((int) ($ticket['status'] ?? 0), $this->allowedStatuses, true))
                ->when($this->categoryFilter, function ($collection) {
                    return $collection->filter(function ($ticket) {
                        $category = (string) ($ticket['itilcategories_id'] ?? '');

                        return Str::contains(Str::lower($category), Str::lower($this->categoryFilter));
                    });
                })
                ->map(fn ($ticket) => $this->normalizeTicket($ticket))
                ->sortByDesc('date')
                ->values()
                ->all();

            return ['ok' => true, 'tickets' => $tickets];
        } catch (Throwable $e) {
            Log::error('GLPI feed: erro ao consultar tickets', ['message' => $e->getMessage()]);

            return [
                'ok' => false,
                'tickets' => [],
                'error' => 'Erro de comunicação com o GLPI. Tente novamente em instantes.',
            ];
        } finally {
            if ($sessionToken) {
                $this->killSession($sessionToken);
            }
        }
    }

    private function initSession(): string
    {
        $response = $this->client()
            ->withHeaders(['Authorization' => "user_token {$this->userToken}"])
            ->get("{$this->apiBaseUrl}/initSession");

        if (!$response->successful() || empty($response->json('session_token'))) {
            throw new \RuntimeException('Falha ao iniciar sessão na API do GLPI (status '.$response->status().').');
        }

        return $response->json('session_token');
    }

    private function killSession(string $sessionToken): void
    {
        try {
            $this->client($sessionToken)->get("{$this->apiBaseUrl}/killSession");
        } catch (Throwable $e) {
            Log::warning('GLPI feed: falha ao encerrar sessão', ['message' => $e->getMessage()]);
        }
    }

    private function client(?string $sessionToken = null)
    {
        $headers = ['Content-Type' => 'application/json'];

        if ($this->appToken) {
            $headers['App-Token'] = $this->appToken;
        }

        if ($sessionToken) {
            $headers['Session-Token'] = $sessionToken;
        }

        return Http::withHeaders($headers)->timeout(8);
    }

    private function normalizeTicket(array $ticket): array
    {
        $id = (int) ($ticket['id'] ?? 0);

        return [
            'id' => $id,
            'title' => (string) ($ticket['name'] ?? 'Sem título'),
            'status' => (int) ($ticket['status'] ?? 0),
            'status_label' => $this->statusLabel((int) ($ticket['status'] ?? 0)),
            'priority_label' => (string) ($ticket['priority'] ?? '—'),
            'category' => (string) ($ticket['itilcategories_id'] ?? '—'),
            'requester' => (string) ($ticket['users_id_recipient'] ?? '—'),
            'date' => (string) ($ticket['date'] ?? ''),
            'url' => $this->frontBaseUrl !== ''
                ? "{$this->frontBaseUrl}/front/ticket.form.php?id={$id}"
                : null,
        ];
    }

    private function statusLabel(int $status): string
    {
        return match ($status) {
            1 => 'Novo',
            2 => 'Processando (atribuído)',
            3 => 'Processando (planejado)',
            4 => 'Pendente',
            5 => 'Solucionado',
            6 => 'Fechado',
            default => 'Desconhecido',
        };
    }
}
