<?php

namespace App\Services;

use App\Models\SupportRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GlpiSupportSyncService
{
    public function syncCreated(SupportRequest $supportRequest): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $message = $supportRequest->messages()->oldest()->value('message');
        $payload = [
            'input' => array_filter([
                'name' => $supportRequest->subject,
                'content' => $message,
                'status' => 1,
                'urgency' => 3,
                'impact' => 3,
                'itilcategories_id' => config('services.glpi.default_support_category_id'),
                'users_id_recipient' => $supportRequest->user_id,
            ], fn ($value) => $value !== null && $value !== ''),
        ];

        $sessionToken = $this->initSession();

        try {
            $response = $this->client($sessionToken)
                ->post("{$this->baseUrl()}/Ticket", $payload);

            if (!$response->successful()) {
                Log::warning('GLPI support sync: falha ao criar ticket local', [
                    'support_request_id' => $supportRequest->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            $ticketId = $this->extractTicketId($response->json());

            if (!$ticketId) {
                return false;
            }

            $supportRequest->forceFill([
                'external_ticket_id' => (string) $ticketId,
            ])->save();

            return true;
        } catch (Throwable $e) {
            Log::warning('GLPI support sync: erro ao criar ticket', [
                'support_request_id' => $supportRequest->id,
                'message' => $e->getMessage(),
            ]);

            return false;
        } finally {
            if ($sessionToken) {
                $this->killSession($sessionToken);
            }
        }
    }

    public function syncReply(SupportRequest $supportRequest, string $message): bool
    {
        if (!$this->isEnabled() || blank($supportRequest->external_ticket_id)) {
            return false;
        }

        $sessionToken = $this->initSession();

        try {
            foreach (['/Ticket/'.$supportRequest->external_ticket_id.'/ITILFollowup', '/Ticket/'.$supportRequest->external_ticket_id.'/Followup'] as $path) {
                $response = $this->client($sessionToken)->post("{$this->baseUrl()}{$path}", [
                    'input' => [
                        'content' => $message,
                    ],
                ]);

                if ($response->successful()) {
                    return true;
                }

                if ($response->status() !== 404) {
                    Log::warning('GLPI support sync: falha ao registrar resposta', [
                        'support_request_id' => $supportRequest->id,
                        'ticket_id' => $supportRequest->external_ticket_id,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    return false;
                }
            }

            return false;
        } catch (Throwable $e) {
            Log::warning('GLPI support sync: erro ao enviar resposta', [
                'support_request_id' => $supportRequest->id,
                'message' => $e->getMessage(),
            ]);

            return false;
        } finally {
            if ($sessionToken) {
                $this->killSession($sessionToken);
            }
        }
    }

    public function syncClose(SupportRequest $supportRequest): bool
    {
        if (!$this->isEnabled() || blank($supportRequest->external_ticket_id)) {
            return false;
        }

        $sessionToken = $this->initSession();

        try {
            $response = $this->client($sessionToken)->put("{$this->baseUrl()}/Ticket/{$supportRequest->external_ticket_id}", [
                'input' => [
                    'status' => 6,
                    'solution' => 'Solicitação concluída pelo atendimento local.',
                ],
            ]);

            if (!$response->successful()) {
                Log::warning('GLPI support sync: falha ao fechar ticket', [
                    'support_request_id' => $supportRequest->id,
                    'ticket_id' => $supportRequest->external_ticket_id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (Throwable $e) {
            Log::warning('GLPI support sync: erro ao fechar ticket', [
                'support_request_id' => $supportRequest->id,
                'message' => $e->getMessage(),
            ]);

            return false;
        } finally {
            if ($sessionToken) {
                $this->killSession($sessionToken);
            }
        }
    }

    private function isEnabled(): bool
    {
        return filled(config('services.glpi.api_base_url'))
            && filled(config('services.glpi.user_token'));
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.glpi.api_base_url'), '/');
    }

    private function initSession(): ?string
    {
        $response = $this->client()
            ->withHeaders(['Authorization' => 'user_token '.config('services.glpi.user_token')])
            ->get("{$this->baseUrl()}/initSession");

        if (!$response->successful()) {
            return null;
        }

        return $response->json('session_token');
    }

    private function killSession(?string $sessionToken): void
    {
        if (!$sessionToken) {
            return;
        }

        try {
            $this->client($sessionToken)->get("{$this->baseUrl()}/killSession");
        } catch (Throwable $e) {
            Log::warning('GLPI support sync: falha ao encerrar sessão da API', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function client(?string $sessionToken = null)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if (filled(config('services.glpi.app_token'))) {
            $headers['App-Token'] = config('services.glpi.app_token');
        }

        if ($sessionToken) {
            $headers['Session-Token'] = $sessionToken;
        }

        return Http::withHeaders($headers)->timeout(8);
    }

    private function extractTicketId(mixed $payload): ?int
    {
        if (!is_array($payload)) {
            return null;
        }

        if (isset($payload['id'])) {
            return (int) $payload['id'];
        }

        if (isset($payload['ticket']['id'])) {
            return (int) $payload['ticket']['id'];
        }

        if (isset($payload['data']['id'])) {
            return (int) $payload['data']['id'];
        }

        return null;
    }
}
