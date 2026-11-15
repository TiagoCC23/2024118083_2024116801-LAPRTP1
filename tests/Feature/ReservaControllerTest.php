<?php

namespace Tests\Feature;

use App\Enums\Disponibilidade;
use App\Enums\EstadoReserva;
use App\Enums\TipoUtilizador;
use App\Models\Edificio;
use App\Models\Horario;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaControllerTest extends TestCase
{
    use RefreshDatabase;

    private function criarUtilizador(string $email, TipoUtilizador $tipo = TipoUtilizador::Estudante, bool $verificado = true): Utilizador
    {
        return Utilizador::create([
            'nome' => 'Utilizador ' . $email,
            'email' => $email,
            'password_hash' => bcrypt('password123'),
            'tipo_user' => $tipo,
            'verificado_em' => $verificado ? now() : null,
        ]);
    }

    private function criarSala(string $nome = 'Sala 101', int $capacidade = 30): Sala
    {
        $edificio = Edificio::firstOrCreate(
            ['sigla' => 'EP'],
            ['nome' => 'Edifício Principal']
        );

        return Sala::create([
            'nome_numero' => $nome,
            'capacidade_maxima' => $capacidade,
            'tipo_sala' => 'Trabalho',
            'piso' => 1,
            'disponibilidade' => Disponibilidade::Disponivel->value,
            'edificio_id' => $edificio->id,
        ]);
    }

    public function test_guest_e_nao_verificado_nao_acedem_a_reservas(): void
    {
        $this->get(route('reservas.index'))->assertRedirect(route('login'));
        $this->get(route('reservas.minhas'))->assertRedirect(route('login'));
        $this->get(route('reservas.create'))->assertRedirect(route('login'));

        $naoVerificado = $this->criarUtilizador('unverified@ufp.edu.pt', TipoUtilizador::Estudante, false);
        $this->actingAs($naoVerificado);

        $this->get(route('reservas.index'))->assertRedirect(route('verification.notice'));
        $this->get(route('reservas.minhas'))->assertRedirect(route('verification.notice'));
    }

    public function test_index_displays_only_own_reservas_for_estudante(): void
    {
        $user1 = $this->criarUtilizador('estudante1@ufp.edu.pt');
        $user2 = $this->criarUtilizador('estudante2@ufp.edu.pt');
        $sala = $this->criarSala();

        $r1 = Reserva::create([
            'data' => now()->addDay()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $sala->id,
            'utilizador_id' => $user1->id,
        ]);

        $r2 = Reserva::create([
            'data' => now()->addDay()->toDateString(),
            'quantidade_pessoas' => 3,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'Leitura',
            'sala_id' => $sala->id,
            'utilizador_id' => $user2->id,
        ]);

        $this->actingAs($user1);
        $response = $this->get(route('reservas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('reservas.index');
        $response->assertViewHas('reservas', fn ($reservas) => $reservas->contains($r1) && !$reservas->contains($r2));
    }

    public function test_index_displays_all_reservas_for_admin_and_supports_search(): void
    {
        $admin = $this->criarUtilizador('admin@ufp.edu.pt', TipoUtilizador::Admin);
        $user1 = $this->criarUtilizador('estudante1@ufp.edu.pt');
        $user2 = $this->criarUtilizador('estudante2@ufp.edu.pt');
        $sala1 = $this->criarSala('Sala Alfa');
        $sala2 = $this->criarSala('Sala Beta');

        $r1 = Reserva::create([
            'data' => now()->addDay()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'Laboratorio',
            'sala_id' => $sala1->id,
            'utilizador_id' => $user1->id,
        ]);

        $r2 = Reserva::create([
            'data' => now()->addDays(2)->toDateString(),
            'quantidade_pessoas' => 3,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'Leitura',
            'sala_id' => $sala2->id,
            'utilizador_id' => $user2->id,
        ]);

        $this->actingAs($admin);

        $this->get(route('reservas.index'))
            ->assertStatus(200)
            ->assertViewHas('reservas', fn ($reservas) => $reservas->contains($r1) && $reservas->contains($r2));

        $this->get(route('reservas.index', ['q' => 'Alfa']))
            ->assertStatus(200)
            ->assertViewHas('reservas', fn ($reservas) => $reservas->contains($r1) && !$reservas->contains($r2));

        $this->get(route('reservas.index', ['q' => 'Leitura']))
            ->assertStatus(200)
            ->assertViewHas('reservas', fn ($reservas) => $reservas->contains($r2) && !$reservas->contains($r1));
    }

    public function test_minhas_reservas_displays_only_authenticated_user_reservas(): void
    {
        $user1 = $this->criarUtilizador('estudante3@ufp.edu.pt');
        $user2 = $this->criarUtilizador('estudante4@ufp.edu.pt');
        $sala = $this->criarSala();

        $r1 = Reserva::create([
            'data' => now()->addDay()->toDateString(),
            'quantidade_pessoas' => 1,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $sala->id,
            'utilizador_id' => $user1->id,
        ]);

        $r2 = Reserva::create([
            'data' => now()->addDay()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'Outro',
            'sala_id' => $sala->id,
            'utilizador_id' => $user2->id,
        ]);

        $this->actingAs($user1);
        $response = $this->get(route('reservas.minhas'));

        $response->assertStatus(200);
        $response->assertViewIs('reservas.index');
        $response->assertViewHas('reservas', fn ($reservas) => $reservas->contains($r1) && !$reservas->contains($r2));
    }

    public function test_create_displays_form_with_selected_date(): void
    {
        $user = $this->criarUtilizador('estudante@ufp.edu.pt');
        $this->criarSala();
        Horario::create(['hora_inicio' => '09:00:00', 'hora_fim' => '10:00:00']);

        $this->actingAs($user);
        $response = $this->get(route('reservas.create', ['data' => '2026-12-10']));

        $response->assertStatus(200);
        $response->assertViewIs('reservas.create');
        $response->assertViewHas('dataSelecionada', '2026-12-10');
        $response->assertViewHasAll(['reserva', 'edificios', 'salas', 'horarios']);
    }

    public function test_store_creates_reserva_with_horarios_and_forces_pendente(): void
    {
        $user = $this->criarUtilizador('estudante_reserva@ufp.edu.pt');
        $outroUser = $this->criarUtilizador('outro@ufp.edu.pt');
        $this->actingAs($user);

        $sala = $this->criarSala('Sala 101', 30);
        $h1 = Horario::create(['hora_inicio' => '10:00:00', 'hora_fim' => '11:00:00']);
        $h2 = Horario::create(['hora_inicio' => '11:00:00', 'hora_fim' => '12:00:00']);

        $payload = [
            'data' => now()->addDay()->toDateString(),
            'quantidade_pessoas' => 5,
            'sala_id' => $sala->id,
            'tipo_de_uso' => 'SalaDeEstudo',
            'horarios' => [$h1->id, $h2->id],
            'estado' => EstadoReserva::Confirmada->value,
            'utilizador_id' => $outroUser->id,
        ];

        $response = $this->post(route('reservas.store'), $payload);

        $response->assertRedirect(route('reservas.index'));
        $response->assertSessionHas('ok');

        $this->assertDatabaseHas('reservas', [
            'quantidade_pessoas' => 5,
            'sala_id' => $sala->id,
            'tipo_de_uso' => 'SalaDeEstudo',
            'utilizador_id' => $user->id,
            'estado' => EstadoReserva::Pendente->value,
        ]);

        $reserva = Reserva::where('utilizador_id', $user->id)->firstOrFail();
        $this->assertDatabaseHas('reserva_horario', ['reserva_id' => $reserva->id, 'horario_id' => $h1->id]);
        $this->assertDatabaseHas('reserva_horario', ['reserva_id' => $reserva->id, 'horario_id' => $h2->id]);
    }

    public function test_store_validation_fails_for_capacity_horarios_and_tipo_de_uso(): void
    {
        $user = $this->criarUtilizador('estudante@ufp.edu.pt');
        $this->actingAs($user);
        $sala = $this->criarSala('Sala Pequena', 4);

        $response = $this->post(route('reservas.store'), [
            'data' => 'invalida',
            'quantidade_pessoas' => 10,
            'tipo_de_uso' => 'Invalido',
            'sala_id' => $sala->id,
            'horarios' => [],
        ]);

        $response->assertSessionHasErrors(['data', 'quantidade_pessoas', 'tipo_de_uso', 'horarios']);
        $this->assertDatabaseCount('reservas', 0);
    }

    public function test_show_autorizacao_para_dono_admin_da_sala_e_terceiros(): void
    {
        $dono = $this->criarUtilizador('dono@ufp.edu.pt');
        $outroEstudante = $this->criarUtilizador('outro@ufp.edu.pt');
        $adminDaSala = $this->criarUtilizador('admin_sala@ufp.edu.pt', TipoUtilizador::Admin);
        $outroAdmin = $this->criarUtilizador('admin_outro@ufp.edu.pt', TipoUtilizador::Admin);

        $sala = $this->criarSala();
        $adminDaSala->salasAdministradas()->attach($sala->id);

        $reserva = Reserva::create([
            'data' => now()->addDay()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $sala->id,
            'utilizador_id' => $dono->id,
        ]);

        $this->actingAs($dono)->get(route('reservas.show', $reserva))
            ->assertStatus(200)
            ->assertViewIs('reservas.show');

        $this->actingAs($adminDaSala)->get(route('reservas.show', $reserva))
            ->assertStatus(200);

        $this->actingAs($outroEstudante)->get(route('reservas.show', $reserva))
            ->assertStatus(403);

        $this->actingAs($outroAdmin)->get(route('reservas.show', $reserva))
            ->assertStatus(403);
    }

    public function test_edit_e_update_respeitam_policy_e_permissoes_de_estado(): void
    {
        $dono = $this->criarUtilizador('dono@ufp.edu.pt');
        $outroEstudante = $this->criarUtilizador('outro@ufp.edu.pt');
        $adminDaSala = $this->criarUtilizador('admin_sala@ufp.edu.pt', TipoUtilizador::Admin);
        $sala = $this->criarSala('Sala 201', 20);
        $adminDaSala->salasAdministradas()->attach($sala->id);

        $h1 = Horario::create(['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);
        $h2 = Horario::create(['hora_inicio' => '09:00:00', 'hora_fim' => '10:00:00']);

        $reserva = Reserva::create([
            'data' => now()->addDay()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $sala->id,
            'utilizador_id' => $dono->id,
        ]);
        $reserva->horarios()->attach($h1->id);

        // Outro estudante não pode editar
        $this->actingAs($outroEstudante)->get(route('reservas.edit', $reserva))->assertStatus(403);

        // Dono pode editar enquanto pendente, mas não pode forçar estado confirmada via update
        $this->actingAs($dono)->get(route('reservas.edit', $reserva))
            ->assertStatus(200)
            ->assertViewIs('reservas.edit');

        $this->actingAs($dono)->put(route('reservas.update', $reserva), [
            'data' => now()->addDays(2)->toDateString(),
            'quantidade_pessoas' => 5,
            'tipo_de_uso' => 'Leitura',
            'sala_id' => $sala->id,
            'horarios' => [$h2->id],
            'estado' => EstadoReserva::Confirmada->value,
        ])->assertRedirect(route('reservas.index'));

        $this->assertDatabaseHas('reservas', [
            'id' => $reserva->id,
            'quantidade_pessoas' => 5,
            'tipo_de_uso' => 'Leitura',
            'estado' => EstadoReserva::Pendente->value,
        ]);
        $this->assertDatabaseMissing('reserva_horario', ['reserva_id' => $reserva->id, 'horario_id' => $h1->id]);
        $this->assertDatabaseHas('reserva_horario', ['reserva_id' => $reserva->id, 'horario_id' => $h2->id]);

        // Admin da sala pode atualizar incluindo o estado para confirmada
        $this->actingAs($adminDaSala)->put(route('reservas.update', $reserva), [
            'data' => now()->addDays(2)->toDateString(),
            'quantidade_pessoas' => 6,
            'tipo_de_uso' => 'Laboratorio',
            'sala_id' => $sala->id,
            'horarios' => [$h1->id],
            'estado' => EstadoReserva::Confirmada->value,
        ])->assertRedirect(route('reservas.index'));

        $this->assertDatabaseHas('reservas', [
            'id' => $reserva->id,
            'quantidade_pessoas' => 6,
            'estado' => EstadoReserva::Confirmada->value,
        ]);

        // Dono já não pode editar após confirmada
        $this->actingAs($dono)->get(route('reservas.edit', $reserva))->assertStatus(403);
        $this->actingAs($dono)->put(route('reservas.update', $reserva), [
            'data' => now()->addDays(3)->toDateString(),
            'quantidade_pessoas' => 2,
            'tipo_de_uso' => 'Outro',
            'sala_id' => $sala->id,
            'horarios' => [$h1->id],
        ])->assertStatus(403);
    }

    public function test_destroy_respeita_policy(): void
    {
        $dono = $this->criarUtilizador('dono@ufp.edu.pt');
        $adminDaSala = $this->criarUtilizador('admin_sala@ufp.edu.pt', TipoUtilizador::Admin);
        $sala = $this->criarSala();
        $adminDaSala->salasAdministradas()->attach($sala->id);

        $reservaPendente = Reserva::create([
            'data' => now()->addDay()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $sala->id,
            'utilizador_id' => $dono->id,
        ]);

        $reservaConfirmada = Reserva::create([
            'data' => now()->addDay()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $sala->id,
            'utilizador_id' => $dono->id,
        ]);

        // Dono não pode apagar reserva confirmada
        $this->actingAs($dono)->delete(route('reservas.destroy', $reservaConfirmada))->assertStatus(403);

        // Dono pode apagar reserva pendente
        $this->actingAs($dono)->delete(route('reservas.destroy', $reservaPendente))
            ->assertRedirect(route('reservas.index'))
            ->assertSessionHas('ok');
        $this->assertDatabaseMissing('reservas', ['id' => $reservaPendente->id]);

        // Admin da sala pode apagar reserva confirmada
        $this->actingAs($adminDaSala)->delete(route('reservas.destroy', $reservaConfirmada))
            ->assertRedirect(route('reservas.index'));
        $this->assertDatabaseMissing('reservas', ['id' => $reservaConfirmada->id]);
    }

    public function test_alterar_estado(): void
    {
        $dono = $this->criarUtilizador('dono_reserva@ufp.edu.pt');
        $this->actingAs($dono);

        $sala = $this->criarSala('Sala 202', 20);
        $reserva = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 3,
            'estado' => EstadoReserva::Pendente->value,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $sala->id,
            'utilizador_id' => $dono->id,
        ]);

        $response = $this->from(route('reservas.show', $reserva))
            ->patch(route('reservas.alterarEstado', $reserva->id), [
                'estado' => EstadoReserva::Confirmada->value,
            ]);

        $response->assertRedirect(route('reservas.show', $reserva));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reservas', [
            'id' => $reserva->id,
            'estado' => EstadoReserva::Confirmada->value,
        ]);
    }

    public function test_alterar_estado_falha_com_estado_invalido(): void
    {
        $dono = $this->criarUtilizador('dono_reserva@ufp.edu.pt');
        $this->actingAs($dono);
        $sala = $this->criarSala();

        $reserva = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 3,
            'estado' => EstadoReserva::Pendente->value,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $sala->id,
            'utilizador_id' => $dono->id,
        ]);

        $response = $this->patch(route('reservas.alterarEstado', $reserva), [
            'estado' => 'inexistente',
        ]);

        $response->assertSessionHasErrors('estado');
        $this->assertDatabaseHas('reservas', [
            'id' => $reserva->id,
            'estado' => EstadoReserva::Pendente->value,
        ]);
    }

    public function test_returns_404_for_nonexistent_reserva(): void
    {
        $this->actingAs($this->criarUtilizador('admin@ufp.edu.pt', TipoUtilizador::Admin));

        $this->get(route('reservas.show', 9999))->assertStatus(404);
        $this->get(route('reservas.edit', 9999))->assertStatus(404);
        $this->put(route('reservas.update', 9999), [])->assertStatus(404);
        $this->delete(route('reservas.destroy', 9999))->assertStatus(404);
        $this->patch(route('reservas.alterarEstado', 9999), [])->assertStatus(404);
    }
}
