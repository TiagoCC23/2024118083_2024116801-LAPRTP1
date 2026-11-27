<?php

namespace Tests\Feature;

use App\Enums\TipoUtilizador;
use App\Http\Controllers\AuthController;
use App\Models\Utilizador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_show_registo_redirects_without_google_session(): void
    {
        $response = $this->get(route('register'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_show_registo_returns_view_with_google_session(): void
    {
        $response = $this->withSession([
            'google_registo' => [
                'nome' => 'Novo Aluno',
                'email' => 'novo@ufp.edu.pt',
                'google_id' => 'gid-123',
            ],
        ])->get(route('register'));

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

    public function test_signin_creates_user_from_google_session_and_authenticates(): void
    {
        $response = $this->withSession([
            'google_registo' => [
                'nome' => 'New User',
                'email' => 'new@ufp.edu.pt',
                'google_id' => 'google-999',
            ],
        ])->post(route('auth.signin'), [
            'nome' => 'New User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('utilizadores', [
            'email' => 'new@ufp.edu.pt',
            'google_id' => 'google-999',
            'nome' => 'New User',
            'tipo_user' => TipoUtilizador::Estudante->value,
        ]);

        $user = Utilizador::where('email', 'new@ufp.edu.pt')->firstOrFail();
        $this->assertNotNull($user->verificado_em);
        $this->assertAuthenticatedAs($user);
        $response->assertSessionMissing('google_registo');
        $response->assertRedirect(route('reservas.index'));
    }

    public function test_signin_validation_fails_with_invalid_data(): void
    {
        $response = $this->withSession([
            'google_registo' => [
                'nome' => 'Aluno',
                'email' => 'aluno@ufp.edu.pt',
                'google_id' => 'google-111',
            ],
        ])->post(route('auth.signin'), [
            'nome' => '',
            'password' => 'curta',
            'password_confirmation' => 'diferente',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['nome', 'password']);
    }

    public function test_logout_clears_session_and_redirects_to_login(): void
    {
        $user = $this->criarUtilizador();
        $this->actingAs($user);

        $response = $this->post(action([AuthController::class, 'logout']));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_logout_or_delete_account(): void
    {
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->post(route('conta.delete'))->assertRedirect(route('login'));
    }

    public function test_delete_account(): void
    {
        $user = $this->criarUtilizador();
        $this->actingAs($user);

        $response = $this->post(action([AuthController::class, 'deleteAccount']));

        $this->assertGuest();
        $this->assertSoftDeleted('utilizadores', ['id' => $user->id], null, 'eliminado_em');
        $this->assertNull(Utilizador::find($user->id));
        $response->assertRedirect(route('salas.index'));
        $response->assertSessionHas('success');
    }

    public function test_authenticated_user_can_access_home(): void
    {
        $user = $this->criarUtilizador();
        $this->actingAs($user);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }
}
