<?php

namespace Tests\Feature;

use App\Enums\TipoUtilizador;
use App\Http\Controllers\AuthController;
use App\Models\Utilizador;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private function criarUtilizador(array $atributos = []): Utilizador
    {
        return Utilizador::create(array_merge([
            'nome' => 'Test User',
            'email' => 'test@ufp.test',
            'password_hash' => Hash::make('password123'),
            'tipo_user' => TipoUtilizador::Estudante,
            'verificado_em' => now(),
        ], $atributos));
    }

    public function test_show_login_returns_view(): void
    {
        $response = $this->get(action([AuthController::class, 'showLogin']));

        $response->assertStatus(200);
        $response->assertViewIs('auth.auth');
        $response->assertViewHas('tab', 'login');
    }

    public function test_show_registo_returns_view(): void
    {
        $response = $this->get(action([AuthController::class, 'showRegisto']));

        $response->assertStatus(200);
        $response->assertViewIs('auth.auth');
        $response->assertViewHas('tab', 'registo');
    }

    public function test_authenticated_user_cannot_access_guest_routes(): void
    {
        $user = $this->criarUtilizador();
        $this->actingAs($user);

        $this->get(route('login'))->assertRedirect();
        $this->get(route('register'))->assertRedirect();
    }

    public function test_login_authenticates_and_redirects(): void
    {
        $user = $this->criarUtilizador();

        $response = $this->post(action([AuthController::class, 'login']), [
            'email' => 'test@ufp.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('reservas.index'));
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->criarUtilizador();

        $response = $this->post(action([AuthController::class, 'login']), [
            'email' => 'test@ufp.test',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_login_validation_requires_email_and_password(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'email-invalido',
            'password' => '',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_soft_deleted_user_cannot_login(): void
    {
        $user = $this->criarUtilizador();
        $user->delete();

        $response = $this->post(route('login'), [
            'email' => 'test@ufp.test',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_signin_creates_user_sends_verification_and_authenticates(): void
    {
        Notification::fake();

        $response = $this->post(action([AuthController::class, 'signin']), [
            'nome' => 'New User',
            'email' => 'new@ufp.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('utilizadores', [
            'email' => 'new@ufp.test',
            'nome' => 'New User',
            'tipo_user' => TipoUtilizador::Estudante->value,
        ]);

        $user = Utilizador::where('email', 'new@ufp.test')->firstOrFail();
        $this->assertNull($user->verificado_em);
        $this->assertAuthenticatedAs($user);
        Notification::assertSentTo($user, VerifyEmail::class);
        $response->assertRedirect(route('verification.notice'));
    }

    public function test_signin_validation_fails_with_invalid_data(): void
    {
        $this->criarUtilizador(['email' => 'duplicado@ufp.test']);

        $response = $this->post(route('auth.signin'), [
            'nome' => '',
            'email' => 'duplicado@ufp.test',
            'password' => 'curta',
            'password_confirmation' => 'diferente',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['nome', 'email', 'password']);
    }

    public function test_logout_clears_session_and_redirects(): void
    {
        $user = $this->criarUtilizador();
        $this->actingAs($user);

        $response = $this->post(action([AuthController::class, 'logout']));

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    public function test_guest_cannot_logout_or_delete_account(): void
    {
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->post(route('conta.delete'))->assertRedirect(route('login'));
    }

    public function test_delete_account(): void
    {
        $user = $this->criarUtilizador(['verificado_em' => null]);
        $this->actingAs($user);

        $response = $this->post(action([AuthController::class, 'deleteAccount']));

        $this->assertGuest();
        $this->assertSoftDeleted('utilizadores', ['id' => $user->id], null, 'eliminado_em');
        $this->assertNull(Utilizador::find($user->id));
        $response->assertRedirect(route('salas.index'));
        $response->assertSessionHas('success');
    }

    public function test_unverified_user_is_redirected_to_verification_notice(): void
    {
        $user = $this->criarUtilizador(['verificado_em' => null]);
        $this->actingAs($user);

        $this->get(route('home'))->assertRedirect(route('verification.notice'));
        $this->get(route('reservas.index'))->assertRedirect(route('verification.notice'));
    }

    public function test_verified_user_can_access_home(): void
    {
        $user = $this->criarUtilizador();
        $this->actingAs($user);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    public function test_verification_notice_page_loads_for_authenticated_user(): void
    {
        $user = $this->criarUtilizador(['verificado_em' => null]);
        $this->actingAs($user);

        $response = $this->get(route('verification.notice'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
    }

    public function test_verification_verify_marks_email_as_verified(): void
    {
        $user = $this->criarUtilizador(['verificado_em' => null]);
        $this->actingAs($user);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($url);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_verification_verify_fails_without_valid_signature(): void
    {
        $user = $this->criarUtilizador(['verificado_em' => null]);
        $this->actingAs($user);

        $response = $this->get(route('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]));

        $response->assertStatus(403);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_verification_resend_sends_notification(): void
    {
        Notification::fake();

        $user = $this->criarUtilizador(['verificado_em' => null]);
        $this->actingAs($user);

        $response = $this->from(route('verification.notice'))
            ->post(route('verification.send'));

        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('message');
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
