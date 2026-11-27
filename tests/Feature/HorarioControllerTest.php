<?php

namespace Tests\Feature;

use App\Enums\TipoUtilizador;
use App\Models\Horario;
use App\Models\Utilizador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HorarioControllerTest extends TestCase
{
    use RefreshDatabase;

    private function criarEstudante(array $atributos = []): Utilizador
    {
        return Utilizador::create(array_merge([
            'nome' => 'Estudante Teste',
            'email' => 'estudante@ufp.edu.pt',
            'password_hash' => bcrypt('password123'),
            'tipo_user' => TipoUtilizador::Estudante,
            'verificado_em' => now(),
        ], $atributos));
    }

    private function criarAdmin(array $atributos = []): Utilizador
    {
        return Utilizador::create(array_merge([
            'nome' => 'Admin Teste',
            'email' => 'admin@ufp.edu.pt',
            'password_hash' => bcrypt('password123'),
            'tipo_user' => TipoUtilizador::Admin,
            'verificado_em' => now(),
        ], $atributos));
    }

    public function test_guest_e_bloqueado(): void
    {
        $horario = Horario::create(['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);

        $this->get(route('horarios.index'))->assertRedirect(route('login'));
        $this->get(route('horarios.show', $horario))->assertRedirect(route('login'));
        $this->get(route('horarios.create'))->assertRedirect(route('login'));
    }

    public function test_estudante_pode_ver_horarios_mas_nao_gerir(): void
    {
        $horario = Horario::create(['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);
        $this->actingAs($this->criarEstudante());

        $this->get(route('horarios.index'))->assertStatus(200);
        $this->get(route('horarios.show', $horario))->assertStatus(200);

        $this->get(route('horarios.create'))->assertStatus(403);
        $this->post(route('horarios.store'), ['hora_inicio' => '10:00', 'hora_fim' => '11:00'])->assertStatus(403);
        $this->get(route('horarios.edit', $horario))->assertStatus(403);
        $this->put(route('horarios.update', $horario), ['hora_inicio' => '09:00', 'hora_fim' => '10:00'])->assertStatus(403);
        $this->delete(route('horarios.destroy', $horario))->assertStatus(403);
    }

    public function test_index_displays_horarios(): void
    {
        $this->actingAs($this->criarEstudante());
        Horario::create(['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);

        $response = $this->get(route('horarios.index'));

        $response->assertStatus(200);
        $response->assertViewIs('horarios.index');
        $response->assertViewHas('horarios');
        $response->assertSee('08:00 - 09:00');
    }

    public function test_create_displays_form_for_admin(): void
    {
        $this->actingAs($this->criarAdmin());

        $response = $this->get(route('horarios.create'));

        $response->assertStatus(200);
        $response->assertViewIs('horarios.create');
        $response->assertViewHas('horario');
    }

    public function test_show_displays_horario(): void
    {
        $this->actingAs($this->criarEstudante());
        $horario = Horario::create(['hora_inicio' => '14:00:00', 'hora_fim' => '15:00:00']);

        $response = $this->get(route('horarios.show', $horario));

        $response->assertStatus(200);
        $response->assertViewIs('horarios.show');
        $response->assertViewHas('horario', fn ($h) => $h->id === $horario->id);
        $response->assertSee('14:00');
    }

    public function test_edit_displays_form_for_admin(): void
    {
        $this->actingAs($this->criarAdmin());
        $horario = Horario::create(['hora_inicio' => '14:00:00', 'hora_fim' => '15:00:00']);

        $response = $this->get(route('horarios.edit', $horario));

        $response->assertStatus(200);
        $response->assertViewIs('horarios.edit');
        $response->assertViewHas('horario', fn ($h) => $h->id === $horario->id);
    }

    public function test_store_creates_horario(): void
    {
        $this->actingAs($this->criarAdmin());

        $response = $this->post(route('horarios.store'), [
            'hora_inicio' => '10:00',
            'hora_fim' => '11:00',
        ]);

        $this->assertDatabaseHas('horarios', [
            'hora_inicio' => '10:00:00',
            'hora_fim' => '11:00:00',
        ]);

        $response->assertRedirect(route('horarios.index'));
        $response->assertSessionHas('success');
    }

    public function test_store_validation_fails_when_hora_fim_not_after_hora_inicio_or_invalid_format(): void
    {
        $this->actingAs($this->criarAdmin());

        $response = $this->post(route('horarios.store'), [
            'hora_inicio' => '12:00',
            'hora_fim' => '11:00',
        ]);
        $response->assertSessionHasErrors(['hora_fim']);

        $response2 = $this->post(route('horarios.store'), [
            'hora_inicio' => '25:99',
            'hora_fim' => '10:00',
        ]);
        $response2->assertSessionHasErrors(['hora_inicio']);

        $this->assertDatabaseCount('horarios', 0);
    }

    public function test_update_modifies_horario(): void
    {
        $this->actingAs($this->criarAdmin());
        $horario = Horario::create(['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);

        $response = $this->put(route('horarios.update', $horario), [
            'hora_inicio' => '09:00',
            'hora_fim' => '10:00',
        ]);

        $this->assertDatabaseHas('horarios', [
            'id' => $horario->id,
            'hora_inicio' => '09:00:00',
            'hora_fim' => '10:00:00',
        ]);

        $response->assertRedirect(route('horarios.index'));
        $response->assertSessionHas('success');
    }

    public function test_update_validation_fails_with_invalid_times(): void
    {
        $this->actingAs($this->criarAdmin());
        $horario = Horario::create(['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);

        $response = $this->put(route('horarios.update', $horario), [
            'hora_inicio' => '10:00',
            'hora_fim' => '10:00',
        ]);

        $response->assertSessionHasErrors(['hora_fim']);
        $this->assertDatabaseHas('horarios', ['id' => $horario->id, 'hora_inicio' => '08:00:00']);
    }

    public function test_destroy_deletes_horario(): void
    {
        $this->actingAs($this->criarAdmin());
        $horario = Horario::create(['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);

        $response = $this->delete(route('horarios.destroy', $horario));

        $this->assertDatabaseMissing('horarios', ['id' => $horario->id]);
        $response->assertRedirect(route('horarios.index'));
        $response->assertSessionHas('success');
    }

    public function test_returns_404_for_nonexistent_horario(): void
    {
        $this->actingAs($this->criarAdmin());

        $this->get(route('horarios.show', 9999))->assertStatus(404);
        $this->get(route('horarios.edit', 9999))->assertStatus(404);
        $this->put(route('horarios.update', 9999), [])->assertStatus(404);
        $this->delete(route('horarios.destroy', 9999))->assertStatus(404);
    }
}
