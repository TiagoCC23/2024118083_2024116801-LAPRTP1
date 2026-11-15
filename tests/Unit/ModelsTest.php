<?php

namespace Tests\Unit;

use App\Enums\EstadoReserva;
use App\Enums\TipoUtilizador;
use App\Models\Avaliacao;
use App\Models\Edificio;
use App\Models\Horario;
use App\Models\Ocupacao;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_edificio_has_many_salas_and_utilizadores(): void
    {
        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 1',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'SalaDeAula',
            'disponibilidade' => 'disponivel',
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);
        $user = Utilizador::create([
            'nome' => 'Utilizador',
            'email' => 'u@ufp.pt',
            'password_hash' => 'secret123',
            'tipo_user' => 'estudante',
            'edificio_id' => $edificio->id,
        ]);

        $this->assertTrue($edificio->salas->contains($sala));
        $this->assertTrue($edificio->utilizadores->contains($user));
        $this->assertInstanceOf(Edificio::class, $sala->edificio);
        $this->assertInstanceOf(Edificio::class, $user->edificio);
    }

    public function test_sala_has_many_reservas_and_belongs_to_many_administradores(): void
    {
        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 1',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'SalaDeAula',
            'disponibilidade' => 'disponivel',
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);
        $admin = Utilizador::create([
            'nome' => 'Admin',
            'email' => 'admin@ufp.pt',
            'password_hash' => 'hash',
            'tipo_user' => 'admin',
        ]);
        $reserva = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 1,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'Outro',
            'sala_id' => $sala->id,
            'utilizador_id' => $admin->id,
        ]);

        $sala->administradores()->attach($admin->id);

        $this->assertTrue($sala->reservas->contains($reserva));
        $this->assertTrue($sala->administradores->contains($admin));
        $this->assertTrue($admin->salasAdministradas->contains($sala));
    }

    public function test_reserva_relationships_casts_and_horarios_ordering(): void
    {
        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 1',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'SalaDeAula',
            'disponibilidade' => 'disponivel',
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);
        $user = Utilizador::create([
            'nome' => 'Test',
            'email' => 'test@ufp.pt',
            'password_hash' => 'hash',
            'tipo_user' => 'estudante',
        ]);
        $reserva = Reserva::create([
            'data' => '2026-12-01',
            'quantidade_pessoas' => 1,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'Outro',
            'sala_id' => $sala->id,
            'utilizador_id' => $user->id,
        ]);

        $hTarde = Horario::create(['hora_inicio' => '14:00:00', 'hora_fim' => '15:00:00']);
        $hManha = Horario::create(['hora_inicio' => '09:00:00', 'hora_fim' => '10:00:00']);
        $reserva->horarios()->attach([$hTarde->id, $hManha->id]);

        $avaliacao = Avaliacao::create(['nota' => 5, 'comentario' => 'Bom', 'reserva_id' => $reserva->id]);

        $this->assertInstanceOf(Sala::class, $reserva->sala);
        $this->assertInstanceOf(Utilizador::class, $reserva->utilizador);
        $this->assertInstanceOf(Carbon::class, $reserva->data);
        $this->assertSame(EstadoReserva::Pendente, $reserva->estado);
        $this->assertNotNull($reserva->criado_em);
        $this->assertEquals($hManha->id, $reserva->horarios->first()->id);
        $this->assertEquals($avaliacao->id, $reserva->avaliacao->id);
        $this->assertTrue($user->reservas->contains($reserva));
    }

    public function test_avaliacao_belongs_to_reserva(): void
    {
        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 1',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'SalaDeAula',
            'disponibilidade' => 'disponivel',
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);
        $user = Utilizador::create([
            'nome' => 'Test',
            'email' => 'test@ufp.pt',
            'password_hash' => 'hash',
            'tipo_user' => 'estudante',
        ]);
        $reserva = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 1,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'Outro',
            'sala_id' => $sala->id,
            'utilizador_id' => $user->id,
        ]);

        $avaliacao = Avaliacao::create(['nota' => 5, 'comentario' => 'Bom', 'reserva_id' => $reserva->id]);

        $this->assertInstanceOf(Reserva::class, $avaliacao->reserva);
    }

    public function test_horario_intervalo_attribute_and_reservas_relation(): void
    {
        $horario = Horario::create(['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);
        $this->assertEquals('08:00 - 09:00', $horario->intervalo);

        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 1',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'SalaDeAula',
            'disponibilidade' => 'disponivel',
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);
        $user = Utilizador::create([
            'nome' => 'Test',
            'email' => 'test@ufp.pt',
            'password_hash' => 'hash',
            'tipo_user' => 'estudante',
        ]);
        $reserva = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 1,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'Outro',
            'sala_id' => $sala->id,
            'utilizador_id' => $user->id,
        ]);
        $horario->reservas()->attach($reserva->id);

        $this->assertTrue($horario->reservas->contains($reserva));
    }

    public function test_utilizador_auth_verification_casts_and_soft_deletes(): void
    {
        $admin = Utilizador::create([
            'nome' => 'Admin',
            'email' => 'admin@ufp.pt',
            'password_hash' => 'password123',
            'tipo_user' => 'admin',
        ]);
        $estudante = Utilizador::create([
            'nome' => 'Estudante',
            'email' => 'estudante@ufp.pt',
            'password_hash' => 'password123',
            'tipo_user' => 'estudante',
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($estudante->isAdmin());
        $this->assertSame(TipoUtilizador::Admin, $admin->tipo_user);
        $this->assertSame('password_hash', $admin->getAuthPasswordName());
        $this->assertSame('', $admin->getRememberTokenName());
        $this->assertTrue(Hash::check('password123', $admin->getAuthPassword()));
        $this->assertNotNull($admin->criado_em);

        // Verificação de email
        $this->assertFalse($estudante->hasVerifiedEmail());
        $this->assertTrue($estudante->markEmailAsVerified());
        $this->assertTrue($estudante->fresh()->hasVerifiedEmail());
        $this->assertInstanceOf(Carbon::class, $estudante->fresh()->verificado_em);

        // SoftDelete na coluna eliminado_em
        $estudante->delete();
        $this->assertSoftDeleted('utilizadores', ['id' => $estudante->id], null, 'eliminado_em');
        $this->assertNotNull(Utilizador::withTrashed()->find($estudante->id)->eliminado_em);
    }

    public function test_ocupacao_belongs_to_sala_and_casts_dates(): void
    {
        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 1',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'SalaDeAula',
            'disponibilidade' => 'disponivel',
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);

        $ocupacao = Ocupacao::create([
            'data_inicio' => now(),
            'data_fim' => now()->addHour(),
            'tipo_ocupacao' => 'Aula',
            'estado' => 'Confirmada',
            'sala_id' => $sala->id,
        ]);

        $this->assertInstanceOf(Sala::class, $ocupacao->sala);
        $this->assertInstanceOf(Carbon::class, $ocupacao->data_inicio);
        $this->assertInstanceOf(Carbon::class, $ocupacao->data_fim);
    }
}
