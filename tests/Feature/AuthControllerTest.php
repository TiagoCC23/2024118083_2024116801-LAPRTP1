<?php

namespace Tests\Feature;

use App\Models\Utilizador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_login_returns_view()
    {
        $response = $this->get(action([\App\Http\Controllers\AuthController::class, 'showLogin']));
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_show_registo_returns_view()
    {
        $response = $this->get(action([\App\Http\Controllers\AuthController::class, 'showRegisto']));
        $response->assertStatus(200);
        $response->assertViewIs('auth.registo');
    }

    public function test_login_authenticates_and_redirects()
    {
        $user = Utilizador::create([
            'nome' => 'Test User',
            'email' => 'test@ufp.test',
            'password_hash' => Hash::make('password123'),
            'tipo_user' => 'estudante',
        ]);

        $response = $this->post(action([\App\Http\Controllers\AuthController::class, 'login']), [
            'email' => 'test@ufp.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('reservas.index'));
    }

    public function test_login_fails_with_invalid_credentials()
    {
        Utilizador::create([
            'nome' => 'Test User',
            'email' => 'test@ufp.test',
            'password_hash' => Hash::make('password123'),
            'tipo_user' => 'estudante',
        ]);

        $response = $this->post(action([\App\Http\Controllers\AuthController::class, 'login']), [
            'email' => 'test@ufp.test',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_signin_creates_user_and_authenticates()
    {
        $response = $this->post(action([\App\Http\Controllers\AuthController::class, 'signin']), [
            'nome' => 'New User',
            'email' => 'new@ufp.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'tipo_user' => 'estudante',
        ]);

        $this->assertDatabaseHas('utilizadores', [
            'email' => 'new@ufp.test',
            'nome' => 'New User',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('reservas.index'));
    }

    public function test_logout_clears_session_and_redirects()
    {
        $user = Utilizador::create([
            'nome' => 'Test User',
            'email' => 'test@ufp.test',
            'password_hash' => Hash::make('password123'),
            'tipo_user' => 'estudante',
        ]);

        $this->actingAs($user);

        $response = $this->post(action([\App\Http\Controllers\AuthController::class, 'logout']));

        $this->assertGuest();
        $response->assertRedirect(route('salas.index'));
    }

    public function test_delete_account()
    {
        $user = Utilizador::create([
            'nome' => 'Test User',
            'email' => 'test@ufp.test',
            'password_hash' => Hash::make('password123'),
            'tipo_user' => 'estudante',
        ]);

        $this->actingAs($user);

        $response = $this->post(action([\App\Http\Controllers\AuthController::class, 'deleteAccount']));

        $this->assertGuest();
        $this->assertSoftDeleted('utilizadores', ['id' => $user->id]);
        $response->assertRedirect(route('salas.index'));
    }
}
