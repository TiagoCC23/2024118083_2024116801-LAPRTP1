<?php

namespace Tests\Feature;

use App\Enums\TipoUtilizador;
use App\Models\Edificio;
use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdificioControllerTest extends TestCase
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

    public function test_guest_e_redirecionado_para_login(): void
    {
        $edificio = Edificio::create(['nome' => 'Edifício Central', 'sigla' => 'ED01']);

        $this->get(route('edificios.index'))->assertRedirect(route('login'));
        $this->get(route('edificios.show', $edificio))->assertRedirect(route('login'));
        $this->get(route('edificios.create'))->assertRedirect(route('login'));
        $this->post(route('edificios.store'), [])->assertRedirect(route('login'));
        $this->get(route('edificios.edit', $edificio))->assertRedirect(route('login'));
        $this->put(route('edificios.update', $edificio), [])->assertRedirect(route('login'));
        $this->delete(route('edificios.destroy', $edificio))->assertRedirect(route('login'));
    }

    public function test_utilizador_nao_verificado_e_redirecionado_para_verificacao(): void
    {
        $user = $this->criarEstudante(['verificado_em' => null]);
        $this->actingAs($user);

        $this->get(route('edificios.index'))->assertRedirect(route('verification.notice'));
    }

    public function test_estudante_pode_ver_mas_nao_gerir_edificios(): void
    {
        $estudante = $this->criarEstudante();
        $edificio = Edificio::create(['nome' => 'Edifício Central', 'sigla' => 'ED01']);
        $this->actingAs($estudante);

        $this->get(route('edificios.index'))->assertStatus(200);
        $this->get(route('edificios.show', $edificio))->assertStatus(200);

        $this->get(route('edificios.create'))->assertStatus(403);
        $this->post(route('edificios.store'), ['nome' => 'Novo', 'sigla' => 'NOV'])->assertStatus(403);
        $this->get(route('edificios.edit', $edificio))->assertStatus(403);
        $this->put(route('edificios.update', $edificio), ['nome' => 'Alt', 'sigla' => 'ALT'])->assertStatus(403);
        $this->delete(route('edificios.destroy', $edificio))->assertStatus(403);
    }

    public function test_index_de_edificios_retorna_view_com_lista(): void
    {
        $this->actingAs($this->criarEstudante());
        Edificio::create([
            'nome' => 'Edifício Central',
            'sigla' => 'ED01',
            'imagem' => 'central.png',
        ]);

        $response = $this->get(route('edificios.index'));

        $response->assertStatus(200);
        $response->assertViewIs('edificios.index');
        $response->assertViewHas('edificios');
        $response->assertSee('Edifício Central');
    }

    public function test_create_retorna_formulario(): void
    {
        $this->actingAs($this->criarAdmin());

        $response = $this->get(route('edificios.create'));

        $response->assertStatus(200);
        $response->assertViewIs('edificios.create');
        $response->assertViewHas('edificio');
    }

    public function test_store_cria_edificio_com_sucesso(): void
    {
        $this->actingAs($this->criarAdmin());

        $payload = [
            'nome' => 'Edifício Tecnológico',
            'sigla' => 'EDTEC',
            'imagem' => 'tec.png',
        ];

        $response = $this->post(route('edificios.store'), $payload);

        $response->assertRedirect(route('edificios.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('edificios', [
            'nome' => 'Edifício Tecnológico',
            'sigla' => 'EDTEC',
            'imagem' => 'tec.png',
        ]);
    }

    public function test_store_falha_quando_campos_obrigatorios_nao_sao_enviados(): void
    {
        $this->actingAs($this->criarAdmin());

        $response = $this->post(route('edificios.store'), []);

        $response->assertSessionHasErrors(['nome', 'sigla']);
        $this->assertDatabaseCount('edificios', 0);
    }

    public function test_store_falha_se_sigla_tiver_mais_de_5_caracteres(): void
    {
        $this->actingAs($this->criarAdmin());

        $response = $this->post(route('edificios.store'), [
            'nome' => 'Edifício Novo',
            'sigla' => 'SIGLA_LONGA',
        ]);

        $response->assertSessionHasErrors(['sigla']);
        $this->assertDatabaseCount('edificios', 0);
    }

    public function test_store_falha_quando_nome_ou_sigla_ja_existem(): void
    {
        $this->actingAs($this->criarAdmin());
        Edificio::create(['nome' => 'Edifício Sede', 'sigla' => 'UFP']);

        $response = $this->post(route('edificios.store'), [
            'nome' => 'Edifício Sede',
            'sigla' => 'UFP',
        ]);

        $response->assertSessionHasErrors(['nome', 'sigla']);
        $this->assertDatabaseCount('edificios', 1);
    }

    public function test_show_exibe_edificio_existente_e_salas(): void
    {
        $this->actingAs($this->criarEstudante());

        $edificio = Edificio::create([
            'nome' => 'Edifício Biblioteca',
            'sigla' => 'BIB',
            'imagem' => null,
        ]);

        Sala::create([
            'nome_numero' => 'Sala B1',
            'capacidade_maxima' => 25,
            'tipo_sala' => 'Trabalho',
            'disponibilidade' => 'disponivel',
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);

        $response = $this->get(route('edificios.show', $edificio->id));

        $response->assertStatus(200);
        $response->assertViewIs('edificios.show');
        $response->assertViewHas('edificio', fn ($viewEdificio) => $viewEdificio->id === $edificio->id);
        $response->assertSee('Sala B1');
    }

    public function test_edit_exibe_formulario_de_edicao(): void
    {
        $this->actingAs($this->criarAdmin());

        $edificio = Edificio::create([
            'nome' => 'Edifício Antigo',
            'sigla' => 'ANT',
            'imagem' => null,
        ]);

        $response = $this->get(route('edificios.edit', $edificio->id));

        $response->assertStatus(200);
        $response->assertViewIs('edificios.edit');
        $response->assertViewHas('edificio');
    }

    public function test_update_atualiza_edificio_com_sucesso_e_ignora_proprio_registo_na_unicidade(): void
    {
        $this->actingAs($this->criarAdmin());

        $edificio = Edificio::create([
            'nome' => 'Nome Antigo',
            'sigla' => 'OLD',
            'imagem' => null,
        ]);

        $response = $this->put(route('edificios.update', $edificio->id), [
            'nome' => 'Nome Antigo',
            'sigla' => 'OLD',
            'imagem' => 'nova.png',
        ]);

        $response->assertRedirect(route('edificios.index'));
        $response->assertSessionHas('success', 'Atualizado com Sucesso');

        $this->assertDatabaseHas('edificios', [
            'id' => $edificio->id,
            'nome' => 'Nome Antigo',
            'sigla' => 'OLD',
            'imagem' => 'nova.png',
        ]);
    }

    public function test_update_falha_com_dados_invalidos(): void
    {
        $this->actingAs($this->criarAdmin());

        $edificio = Edificio::create([
            'nome' => 'Edifício A',
            'sigla' => 'EDA',
            'imagem' => null,
        ]);

        $response = $this->put(route('edificios.update', $edificio->id), [
            'nome' => '',
            'sigla' => 'SIGLA_MUITO_LONGA',
        ]);

        $response->assertSessionHasErrors(['nome', 'sigla']);
        $this->assertDatabaseHas('edificios', ['nome' => 'Edifício A']);
    }

    public function test_destroy_remove_edificio_do_banco(): void
    {
        $this->actingAs($this->criarAdmin());

        $edificio = Edificio::create([
            'nome' => 'Edifício a Eliminar',
            'sigla' => 'DEL',
            'imagem' => null,
        ]);

        $response = $this->delete(route('edificios.destroy', $edificio->id));

        $response->assertRedirect(route('edificios.index'));
        $response->assertSessionHas('success', 'Registo apagado com sucesso');
        $this->assertDatabaseMissing('edificios', ['id' => $edificio->id]);
    }

    public function test_retorna_404_ao_tentar_acessar_edificio_inexistente(): void
    {
        $this->actingAs($this->criarAdmin());
        $idInexistente = 9999;

        $this->get(route('edificios.show', $idInexistente))->assertStatus(404);
        $this->get(route('edificios.edit', $idInexistente))->assertStatus(404);
        $this->put(route('edificios.update', $idInexistente), ['nome' => 'x', 'sigla' => 'X'])->assertStatus(404);
        $this->delete(route('edificios.destroy', $idInexistente))->assertStatus(404);
    }
}
