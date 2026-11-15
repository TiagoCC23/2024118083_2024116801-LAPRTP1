<?php

namespace Tests\Feature;

use App\Enums\EstadoReserva;
use App\Enums\TipoUtilizador;
use App\Models\Avaliacao;
use App\Models\Edificio;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvaliacaoControllerTest extends TestCase
{
    use RefreshDatabase;

    private function criarUtilizador(string $email, bool $verificado = true): Utilizador
    {
        return Utilizador::create([
            'nome' => 'Avaliador ' . $email,
            'email' => $email,
            'password_hash' => bcrypt('password'),
            'tipo_user' => TipoUtilizador::Estudante,
            'verificado_em' => $verificado ? now() : null,
        ]);
    }

    private function criarSala(): Sala
    {
        $edificio = Edificio::firstOrCreate(['sigla' => 'ED'], ['nome' => 'Edificio']);

        return Sala::create([
            'nome_numero' => 'S1',
            'capacidade_maxima' => 10,
            'tipo_sala' => 'Trabalho',
            'piso' => 1,
            'disponibilidade' => 'disponivel',
            'edificio_id' => $edificio->id,
        ]);
    }

    private function criarReserva(Utilizador $user, Sala $sala, EstadoReserva $estado = EstadoReserva::Confirmada): Reserva
    {
        return Reserva::create([
            'data' => now()->subDay()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => $estado,
            'tipo_de_uso' => 'Outro',
            'sala_id' => $sala->id,
            'utilizador_id' => $user->id,
        ]);
    }

    public function test_guest_e_nao_verificado_nao_acedem_a_avaliacoes(): void
    {
        $this->get(route('avaliacoes.index'))->assertRedirect(route('login'));
        $this->get(route('avaliacoes.minhas'))->assertRedirect(route('login'));
        $this->get(route('avaliacoes.create'))->assertRedirect(route('login'));

        $naoVerificado = $this->criarUtilizador('unverified@ufp.edu.pt', false);
        $this->actingAs($naoVerificado);

        $this->get(route('avaliacoes.index'))->assertRedirect(route('verification.notice'));
        $this->get(route('avaliacoes.minhas'))->assertRedirect(route('verification.notice'));
    }

    public function test_index_displays_avaliacoes(): void
    {
        $user = $this->criarUtilizador('u1@ufp.edu.pt');
        $sala = $this->criarSala();
        $reserva = $this->criarReserva($user, $sala);
        $avaliacao = Avaliacao::create(['nota' => 5, 'comentario' => 'Ótima sala', 'reserva_id' => $reserva->id]);

        $this->actingAs($user);
        $response = $this->get(route('avaliacoes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('avaliacoes.index');
        $response->assertViewHas('avaliacoes', fn ($a) => $a->contains($avaliacao));
        $response->assertSee('Ótima sala');
    }

    public function test_minhas_avaliacoes_displays_only_user_avaliacoes(): void
    {
        $user1 = $this->criarUtilizador('avaliador1@ufp.edu.pt');
        $user2 = $this->criarUtilizador('avaliador2@ufp.edu.pt');
        $sala = $this->criarSala();

        $r1 = $this->criarReserva($user1, $sala);
        $r2 = $this->criarReserva($user2, $sala);

        $av1 = Avaliacao::create(['nota' => 5, 'comentario' => 'Minha avaliação', 'reserva_id' => $r1->id]);
        $av2 = Avaliacao::create(['nota' => 2, 'comentario' => 'Avaliação de outro', 'reserva_id' => $r2->id]);

        $this->actingAs($user1);
        $response = $this->get(route('avaliacoes.minhas'));

        $response->assertStatus(200);
        $response->assertViewIs('avaliacoes.self');
        $response->assertViewHas('avaliacoes', fn ($a) => $a->contains($av1) && !$a->contains($av2));
    }

    public function test_create_displays_only_confirmed_unrated_reservas_of_user(): void
    {
        $user = $this->criarUtilizador('u_create@ufp.edu.pt');
        $outroUser = $this->criarUtilizador('outro@ufp.edu.pt');
        $sala = $this->criarSala();

        $reservaElegivel = $this->criarReserva($user, $sala, EstadoReserva::Confirmada);
        $reservaPendente = $this->criarReserva($user, $sala, EstadoReserva::Pendente);
        $reservaJaAvaliada = $this->criarReserva($user, $sala, EstadoReserva::Confirmada);
        Avaliacao::create(['nota' => 4, 'comentario' => 'Já avaliada', 'reserva_id' => $reservaJaAvaliada->id]);
        $reservaOutroUser = $this->criarReserva($outroUser, $sala, EstadoReserva::Confirmada);

        $this->actingAs($user);
        $response = $this->get(route('avaliacoes.create'));

        $response->assertStatus(200);
        $response->assertViewIs('avaliacoes.create');
        $response->assertViewHas('avaliacao');
        $response->assertViewHas('reservas', function ($reservas) use (
            $reservaElegivel,
            $reservaPendente,
            $reservaJaAvaliada,
            $reservaOutroUser
        ) {
            return $reservas->contains($reservaElegivel)
                && !$reservas->contains($reservaPendente)
                && !$reservas->contains($reservaJaAvaliada)
                && !$reservas->contains($reservaOutroUser);
        });
    }

    public function test_store_creates_avaliacao(): void
    {
        $user = $this->criarUtilizador('u2@ufp.test');
        $this->actingAs($user);
        $sala = $this->criarSala();
        $reserva = $this->criarReserva($user, $sala);

        $response = $this->post(route('avaliacoes.store'), [
            'nota' => 4,
            'comentario' => 'Muito bom',
            'reserva_id' => $reserva->id,
        ]);

        $this->assertDatabaseHas('avaliacoes', [
            'nota' => 4,
            'comentario' => 'Muito bom',
            'reserva_id' => $reserva->id,
        ]);

        $response->assertRedirect(route('avaliacoes.index'));
        $response->assertSessionHas('success');
    }

    public function test_store_validation_fails_for_invalid_nota_or_duplicate_reserva(): void
    {
        $user = $this->criarUtilizador('u_val@ufp.test');
        $this->actingAs($user);
        $sala = $this->criarSala();
        $reserva = $this->criarReserva($user, $sala);

        Avaliacao::create(['nota' => 5, 'comentario' => 'Inicial', 'reserva_id' => $reserva->id]);

        $response = $this->post(route('avaliacoes.store'), [
            'nota' => 6,
            'comentario' => str_repeat('a', 513),
            'reserva_id' => $reserva->id,
        ]);

        $response->assertSessionHasErrors(['nota', 'comentario', 'reserva_id']);
        $this->assertDatabaseCount('avaliacoes', 1);
    }

    public function test_show_displays_avaliacao(): void
    {
        $user = $this->criarUtilizador('u_show@ufp.test');
        $this->actingAs($user);
        $sala = $this->criarSala();
        $reserva = $this->criarReserva($user, $sala);
        $avaliacao = Avaliacao::create(['nota' => 5, 'comentario' => 'Excelente espaço', 'reserva_id' => $reserva->id]);

        $response = $this->get(route('avaliacoes.show', $avaliacao));

        $response->assertStatus(200);
        $response->assertViewIs('avaliacoes.show');
        $response->assertViewHas('avaliacao', fn ($a) => $a->id === $avaliacao->id);
        $response->assertSee('Excelente espaço');
    }

    public function test_edit_displays_form(): void
    {
        $user = $this->criarUtilizador('u_edit@ufp.test');
        $this->actingAs($user);
        $sala = $this->criarSala();
        $reserva = $this->criarReserva($user, $sala);
        $avaliacao = Avaliacao::create(['nota' => 3, 'comentario' => 'Medio', 'reserva_id' => $reserva->id]);

        $response = $this->get(route('avaliacoes.edit', $avaliacao));

        $response->assertStatus(200);
        $response->assertViewIs('avaliacoes.edit');
        $response->assertViewHas('avaliacao', fn ($a) => $a->id === $avaliacao->id);
        $response->assertViewHas('reservas', fn ($r) => $r->contains($reserva));
    }

    public function test_update_modifies_avaliacao(): void
    {
        $user = $this->criarUtilizador('u3@ufp.test');
        $this->actingAs($user);
        $sala = $this->criarSala();
        $reserva = $this->criarReserva($user, $sala);
        $avaliacao = Avaliacao::create(['nota' => 3, 'comentario' => 'Medio', 'reserva_id' => $reserva->id]);

        $response = $this->put(route('avaliacoes.update', $avaliacao), [
            'nota' => 5,
            'comentario' => 'Excelente',
            'reserva_id' => $reserva->id,
        ]);

        $this->assertDatabaseHas('avaliacoes', [
            'id' => $avaliacao->id,
            'nota' => 5,
            'comentario' => 'Excelente',
        ]);

        $response->assertRedirect(route('avaliacoes.index'));
        $response->assertSessionHas('success');
    }

    public function test_destroy_deletes_avaliacao(): void
    {
        $user = $this->criarUtilizador('u4@ufp.test');
        $this->actingAs($user);
        $sala = $this->criarSala();
        $reserva = $this->criarReserva($user, $sala);
        $avaliacao = Avaliacao::create(['nota' => 3, 'comentario' => 'Medio', 'reserva_id' => $reserva->id]);

        $response = $this->delete(route('avaliacoes.destroy', $avaliacao));

        $this->assertDatabaseMissing('avaliacoes', ['id' => $avaliacao->id]);
        $response->assertRedirect(route('avaliacoes.index'));
        $response->assertSessionHas('success');
    }

    public function test_returns_404_for_nonexistent_avaliacao(): void
    {
        $this->actingAs($this->criarUtilizador('u404@ufp.test'));

        $this->get(route('avaliacoes.show', 9999))->assertStatus(404);
        $this->get(route('avaliacoes.edit', 9999))->assertStatus(404);
        $this->put(route('avaliacoes.update', 9999), [])->assertStatus(404);
        $this->delete(route('avaliacoes.destroy', 9999))->assertStatus(404);
    }
}
