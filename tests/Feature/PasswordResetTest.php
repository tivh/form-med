<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Filament\Facades\Filament;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_password_reset_request_page_can_be_rendered(): void
    {
        $response = $this->get('/filament/password-reset/request');
        $response->assertOk();
    }

    public function test_login_page_contains_password_reset_link(): void
    {
        $response = $this->get('/filament/login');
        $response->assertOk()
            ->assertSee('/filament/password-reset/request');
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'admin@vitoriahospitalar.com.br',
            'is_super_admin' => true,
        ]);

        Livewire::test(RequestPasswordReset::class)
            ->set('data.email', 'admin@vitoriahospitalar.com.br')
            ->call('request')
            ->assertHasNoFormErrors();

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_password_reset_notification_contains_proper_mail_content(): void
    {
        $user = User::factory()->create([
            'name' => 'Dr. Thiago',
            'email' => 'thiago@vitoriahospitalar.com.br',
        ]);

        $token = 'test-reset-token-12345';
        $notification = new ResetPasswordNotification($token);
        $mail = $notification->toMail($user);

        $this->assertEquals('Redefinição de Senha - Vitória Hospitalar', $mail->subject);
        $this->assertStringContainsString('Dr. Thiago', $mail->greeting);
        $this->assertStringContainsString('Redefinir Minha Senha', $mail->actionText);
        $this->assertStringContainsString('token=test-reset-token-12345', $mail->actionUrl);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@vitoriahospitalar.com.br',
            'password' => Hash::make('SenhaAntiga123!'),
        ]);

        $token = Password::broker()->createToken($user);
        $signedUrl = Filament::getPanel('admin')->getResetPasswordUrl($token, $user);

        $response = $this->get($signedUrl);
        $response->assertOk();

        Livewire::withQueryParams([
            'email' => $user->email,
            'token' => $token,
        ])
            ->test(ResetPassword::class)
            ->fillForm([
                'password' => 'NovaSenhaSegura456!',
                'passwordConfirmation' => 'NovaSenhaSegura456!',
            ])
            ->call('resetPassword')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('NovaSenhaSegura456!', $user->fresh()->password));
    }

    public function test_password_cannot_be_reset_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@vitoriahospitalar.com.br',
            'password' => Hash::make('SenhaAntiga123!'),
        ]);

        Livewire::withQueryParams([
            'email' => $user->email,
            'token' => 'invalid-token',
        ])
            ->test(ResetPassword::class)
            ->fillForm([
                'password' => 'NovaSenhaSegura456!',
                'passwordConfirmation' => 'NovaSenhaSegura456!',
            ])
            ->call('resetPassword');

        $this->assertTrue(Hash::check('SenhaAntiga123!', $user->fresh()->password));
    }
}
