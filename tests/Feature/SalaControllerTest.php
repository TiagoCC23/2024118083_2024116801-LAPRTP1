<?php

namespace Tests\Feature;

use App\Enums\Disponibilidade;
use App\Enums\EstadoReserva;
use App\Enums\TipoUtilizador;
use App\Models\Edificio;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalaControllerTest extends TestCase
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

    private function criarSala(Edificio $edificio, array $atributos = []): Sala
    {
        return Sala::create(array_merge([
            'nome_numero' => 'Sala 1',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'SalaDeAula',
            'disponibilidade' => Disponibilidade::Disponivel->value,
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ], $atributos));
    }

    public function test_guest_e_bloqueado(): void
    {
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $sala = $this->criarSala($edificio);

        $this->get(route('salas.index'))->assertRedirect(route('login'));
        $this->get(route('salas.show', $sala))->assertRedirect(route('login'));
        $this->get(route('salas.disponibilidade'))->assertRedirect(route('login'));
        $this->post(route('salas.store'), [])->assertRedirect(route('login'));
    }

    public function test_estudante_pode_consultar_salas_mas_nao_gerir(): void
    {
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $sala = $this->criarSala($edificio);
        $this->actingAs($this->criarEstudante());

        $this->get(route('salas.index'))->assertStatus(200);
        $this->get(route('salas.show', $sala))->assertStatus(200);
        $this->get(route('salas.disponibilidade'))->assertStatus(200);

        $this->get(route('salas.create'))->assertStatus(403);
        $this->post(route('salas.store'), [])->assertStatus(403);
        $this->get(route('salas.edit', $sala))->assertStatus(403);
        $this->put(route('salas.update', $sala), [])->assertStatus(403);
        $this->delete(route('salas.destroy', $sala))->assertStatus(403);
    }

    public function test_index_displays_salas(): void
    {
        $this->actingAs($this->criarEstudante());
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $this->criarSala($edificio);

        $response = $this->get(route('salas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('salas.index');
        $response->assertViewHas('salas');
        $response->assertSee('Sala 1');
    }

    public function test_create_displays_form_for_admin(): void
    {
        $this->actingAs($this->criarAdmin());
        Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);

        $response = $this->get(route('salas.create'));

        $response->assertStatus(200);
        $response->assertViewIs('salas.create');
        $response->assertViewHasAll(['sala', 'edificios', 'disponibilidades']);
    }

    public function test_show_displays_sala(): void
    {
        $this->actingAs($this->criarEstudante());
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $sala = $this->criarSala($edificio, ['nome_numero' => 'Sala 305']);

        $response = $this->get(route('salas.show', $sala));

        $response->assertStatus(200);
        $response->assertViewIs('salas.show');
        $response->assertViewHas('sala', fn ($s) => $s->id === $sala->id);
        $response->assertSee('Sala 305');
    }

    public function test_edit_displays_form_for_admin(): void
    {
        $this->actingAs($this->criarAdmin());
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $sala = $this->criarSala($edificio);

        $response = $this->get(route('salas.edit', $sala));

        $response->assertStatus(200);
        $response->assertViewIs('salas.edit');
        $response->assertViewHasAll(['sala', 'edificios', 'disponibilidades']);
    }

    public function test_store_creates_sala(): void
    {
        $this->actingAs($this->criarAdmin());
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);

        $response = $this->post(route('salas.store'), [
            'nome_numero' => 'Sala 2',
            'capacidade_maxima' => 30,
            'tipo_sala' => 'Exame',
            'disponibilidade' => Disponibilidade::Disponivel->value,
            'piso' => 2,
            'edificio_id' => $edificio->id,
        ]);

        $this->assertDatabaseHas('salas', [
            'nome_numero' => 'Sala 2',
            'capacidade_maxima' => 30,
            'tipo_sala' => 'Exame',
            'edificio_id' => $edificio->id,
        ]);

        $response->assertRedirect(route('salas.index'));
        $response->assertSessionHas('success');
    }

    public function test_store_validation_fails_with_invalid_fields_or_duplicate_in_same_edificio(): void
    {
        $this->actingAs($this->criarAdmin());
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $this->criarSala($edificio, ['nome_numero' => 'Sala 101']);

        $response = $this->post(route('salas.store'), [
            'nome_numero' => 'Sala 101',
            'capacidade_maxima' => 0,
            'tipo_sala' => 'Invalido',
            'disponibilidade' => 'invalido',
            'piso' => 'abc',
            'edificio_id' => $edificio->id,
        ]);

        $response->assertSessionHasErrors([
            'nome_numero',
            'capacidade_maxima',
            'tipo_sala',
            'disponibilidade',
            'piso',
        ]);
    }

    public function test_store_allows_same_nome_numero_in_different_edificios(): void
    {
        $this->actingAs($this->criarAdmin());
        $ed1 = Edificio::create(['nome' => 'Edificio 1', 'sigla' => 'ED1']);
        $ed2 = Edificio::create(['nome' => 'Edificio 2', 'sigla' => 'ED2']);
        $this->criarSala($ed1, ['nome_numero' => 'Sala 101']);

        $response = $this->post(route('salas.store'), [
            'nome_numero' => 'Sala 101',
            'capacidade_maxima' => 25,
            'tipo_sala' => 'Trabalho',
            'disponibilidade' => Disponibilidade::Disponivel->value,
            'piso' => 1,
            'edificio_id' => $ed2->id,
        ]);

        $response->assertRedirect(route('salas.index'));
        $this->assertDatabaseCount('salas', 2);
    }

    public function test_update_modifies_sala(): void
    {
        $this->actingAs($this->criarAdmin());
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $sala = $this->criarSala($edificio);

        $response = $this->put(route('salas.update', $sala), [
            'nome_numero' => 'Sala Modificada',
            'capacidade_maxima' => 40,
            'tipo_sala' => 'Trabalho',
            'disponibilidade' => Disponibilidade::Ocupado->value,
            'piso' => 3,
            'edificio_id' => $edificio->id,
        ]);

        $this->assertDatabaseHas('salas', [
            'id' => $sala->id,
            'nome_numero' => 'Sala Modificada',
            'capacidade_maxima' => 40,
            'disponibilidade' => Disponibilidade::Ocupado->value,
        ]);

        $response->assertRedirect(route('salas.index'));
        $response->assertSessionHas('success');
    }

    public function test_update_allows_keeping_same_nome_numero_in_same_edificio(): void
    {
        $this->actingAs($this->criarAdmin());
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $sala = $this->criarSala($edificio, ['nome_numero' => 'Sala 101']);

        $response = $this->put(route('salas.update', $sala), [
            'nome_numero' => 'Sala 101',
            'capacidade_maxima' => 35,
            'tipo_sala' => 'Outro',
            'disponibilidade' => Disponibilidade::Disponivel->value,
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);

        $response->assertRedirect(route('salas.index'));
        $this->assertDatabaseHas('salas', ['id' => $sala->id, 'capacidade_maxima' => 35]);
    }

    public function test_destroy_deletes_sala(): void
    {
        $this->actingAs($this->criarAdmin());
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $sala = $this->criarSala($edificio);

        $response = $this->delete(route('salas.destroy', $sala));

        $this->assertDatabaseMissing('salas', ['id' => $sala->id]);
        $response->assertRedirect(route('salas.index'));
        $response->assertSessionHas('success');
    }

    public function test_check_disponibilidade_filters_capacity_and_reservas(): void
    {
        $user = $this->criarEstudante();
        $this->actingAs($user);

        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $dataAlvo = now()->addDays(2)->toDateString();

        $salaDisponivel = $this->criarSala($edificio, ['nome_numero' => 'Sala Livre', 'capacidade_maxima' => 20]);
        $salaPequena = $this->criarSala($edificio, ['nome_numero' => 'Sala Pequena', 'capacidade_maxima' => 5]);
        $salaOcupadaPendente = $this->criarSala($edificio, ['nome_numero' => 'Sala Pendente', 'capacidade_maxima' => 25]);
        $salaOcupadaConfirmada = $this->criarSala($edificio, ['nome_numero' => 'Sala Confirmada', 'capacidade_maxima' => 25]);
        $salaCancelada = $this->criarSala($edificio, ['nome_numero' => 'Sala Cancelada', 'capacidade_maxima' => 25]);
        $salaOutroDia = $this->criarSala($edificio, ['nome_numero' => 'Sala Outro Dia', 'capacidade_maxima' => 25]);

        Reserva::create([
            'data' => $dataAlvo,
            'quantidade_pessoas' => 4,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $salaOcupadaPendente->id,
            'utilizador_id' => $user->id,
        ]);

        Reserva::create([
            'data' => $dataAlvo,
            'quantidade_pessoas' => 4,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $salaOcupadaConfirmada->id,
            'utilizador_id' => $user->id,
        ]);

        Reserva::create([
            'data' => $dataAlvo,
            'quantidade_pessoas' => 4,
            'estado' => EstadoReserva::Cancelada,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $salaCancelada->id,
            'utilizador_id' => $user->id,
        ]);

        Reserva::create([
            'data' => now()->addDays(5)->toDateString(),
            'quantidade_pessoas' => 4,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $salaOutroDia->id,
            'utilizador_id' => $user->id,
        ]);

        $response = $this->post(route('salas.disponibilidade'), [
            'data' => $dataAlvo,
            'capacidade_maxima' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('salas.disponibilidade');
        $response->assertViewHas('salasDisponiveis', function ($salas) use (
            $salaDisponivel,
            $salaPequena,
            $salaOcupadaPendente,
            $salaOcupadaConfirmada,
            $salaCancelada,
            $salaOutroDia
        ) {
            return $salas->contains($salaDisponivel)
                && $salas->contains($salaCancelada)
                && $salas->contains($salaOutroDia)
                && !$salas->contains($salaPequena)
                && !$salas->contains($salaOcupadaPendente)
                && !$salas->contains($salaOcupadaConfirmada);
        });
    }

    public function test_check_disponibilidade_validation_fails_with_invalid_input(): void
    {
        $this->actingAs($this->criarEstudante());

        $response = $this->post(route('salas.disponibilidade'), [
            'data' => 'nao-e-data',
            'capacidade_maxima' => -1,
        ]);

        $response->assertSessionHasErrors(['data', 'capacidade_maxima']);
    }

    public function test_returns_404_for_nonexistent_sala(): void
    {
        $this->actingAs($this->criarAdmin());

        $this->get(route('salas.show', 9999))->assertStatus(404);
        $this->get(route('salas.edit', 9999))->assertStatus(404);
        $this->put(route('salas.update', 9999), [])->assertStatus(404);
        $this->delete(route('salas.destroy', 9999))->assertStatus(404);
    }
}
