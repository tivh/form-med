<?php

namespace Tests\Feature;

use App\Models\SupportRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SupportRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_support_request(): void
    {
        $user = User::factory()->create([
            'name' => 'Maria',
            'email' => 'maria@teste.com',
        ]);

        $response = $this->actingAs($user)->post(route('support.store'), [
            'subject' => 'Preciso de uma licença',
            'message' => 'Gostaria de solicitar uma licença para uso do sistema.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('support_requests', [
            'user_id' => $user->id,
            'subject' => 'Preciso de uma licença',
            'status' => 'new',
        ]);
        $this->assertDatabaseHas('support_messages', [
            'sender_type' => 'user',
            'message' => 'Gostaria de solicitar uma licença para uso do sistema.',
        ]);
    }

    public function test_admin_can_view_support_feed(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@teste.com',
            'form_scope' => null,
        ]);

        $request = SupportRequest::create([
            'user_id' => $admin->id,
            'requester_name' => 'Admin',
            'requester_email' => 'admin@teste.com',
            'subject' => 'Acesso ao sistema',
            'status' => 'new',
            'source' => 'web',
        ]);

        $request->messages()->create([
            'sender_type' => 'user',
            'sender_name' => 'Admin',
            'message' => 'Preciso de acesso ao sistema.',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.support.feed'));

        $response->assertOk();
        $response->assertSeeText('Acesso ao sistema');
    }

    public function test_glpi_feed_only_returns_tickets_created_by_our_support_feed(): void
    {
        config()->set('services.glpi.api_base_url', 'https://glpi.example.com/apirest.php/1.0');
        config()->set('services.glpi.user_token', 'token-123');
        config()->set('services.glpi.app_token', 'app-123');

        SupportRequest::create([
            'user_id' => null,
            'requester_name' => 'João',
            'requester_email' => 'joao@teste.com',
            'subject' => 'Ticket do feed local',
            'status' => 'new',
            'source' => 'web',
            'external_ticket_id' => 101,
        ]);

        SupportRequest::create([
            'user_id' => null,
            'requester_name' => 'Maria',
            'requester_email' => 'maria@teste.com',
            'subject' => 'Ticket externo',
            'status' => 'new',
            'source' => 'web',
            'external_ticket_id' => 999,
        ]);

        Http::fake([
            'https://glpi.example.com/apirest.php/1.0/initSession' => Http::response(['session_token' => 'abc123'], 200),
            'https://glpi.example.com/apirest.php/1.0/Ticket*' => Http::response([
                [
                    'id' => 101,
                    'name' => 'Ticket do feed local',
                    'status' => 1,
                    'priority' => 3,
                    'itilcategories_id' => '12',
                    'users_id_recipient' => 'João',
                    'date' => '2026-08-17 12:00:00',
                ],
                [
                    'id' => 555,
                    'name' => 'Ticket externo aleatório',
                    'status' => 1,
                    'priority' => 2,
                    'itilcategories_id' => '9',
                    'users_id_recipient' => 'Maria',
                    'date' => '2026-08-17 13:00:00',
                ],
            ], 200),
            'https://glpi.example.com/apirest.php/1.0/killSession' => Http::response([], 200),
        ]);

        $result = app(\App\Services\GlpiFeedService::class)->fetchTickets();

        $this->assertTrue($result['ok']);
        $this->assertCount(1, $result['tickets']);
        $this->assertSame(101, $result['tickets'][0]['id']);
        $this->assertSame('Ticket do feed local', $result['tickets'][0]['title']);
    }
}
