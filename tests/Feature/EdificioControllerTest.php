<?php

namespace Tests\Feature;

use App\Models\Edificio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdificioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_de_edificios_retorna_view_com_lista(): void
    {
        Edificio::create([
            'nome' => 'Edifício Central',
            'sigla' => 'ED01',
            'imagem' => 'central.png',
        ]);

        $response = $this->get(route('edificios.index'));

        $response->assertStatus(200);
        $response->assertViewIs('edificios.index');
        $response->assertViewHas('edificios');
    }

    public function test_create_retorna_formulario(): void
    {
        $response = $this->get(route('edificios.create'));

        $response->assertStatus(200);
        $response->assertViewIs('edificios.create');
    }

    public function test_store_cria_edificio_com_sucesso(): void
    {
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
        ]);
    }

    public function test_store_falha_quando_campos_obrigatorios_nao_sao_enviados(): void
    {
        $response = $this->post(route('edificios.store'), []);

        $response->assertSessionHasErrors(['nome', 'sigla']);
        $this->assertDatabaseCount('edificios', 0);
    }

    public function test_store_falha_se_sigla_tiver_mais_de_5_caracteres(): void
    {
        $payload = [
            'nome' => 'Edifício Novo',
            'sigla' => 'SIGLA_LONGA', // Máximo permitido é 5
        ];

        $response = $this->post(route('edificios.store'), $payload);

        $response->assertSessionHasErrors(['sigla']);
        $this->assertDatabaseCount('edificios', 0);
    }

    public function test_show_exibe_edificio_existente(): void
    {
        $edificio = Edificio::create([
            'nome' => 'Edifício Biblioteca',
            'sigla' => 'BIB',
            'imagem' => null,
        ]);

        $response = $this->get(route('edificios.show', $edificio->id));

        $response->assertStatus(200);
        $response->assertViewIs('edificios.show');
        $response->assertViewHas('edificio', function ($viewEdificio) use ($edificio) {
            return $viewEdificio->id === $edificio->id;
        });
    }

    public function test_edit_exibe_formulario_de_edicao(): void
    {
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

    public function test_update_atualiza_edificio_com_sucesso(): void
    {
        $edificio = Edificio::create([
            'nome' => 'Nome Antigo',
            'sigla' => 'OLD',
            'imagem' => null,
        ]);

        $dadosAtualizados = [
            'nome' => 'Nome Atualizado',
            'sigla' => 'NEW',
            'imagem' => 'nova.png',
        ];

        $response = $this->put(route('edificios.update', $edificio->id), $dadosAtualizados);

        $response->assertRedirect(route('edificios.index'));
        $response->assertSessionHas('success', 'Atualizado com Sucesso');

        $this->assertDatabaseHas('edificios', [
            'id' => $edificio->id,
            'nome' => 'Nome Atualizado',
            'sigla' => 'NEW',
        ]);
    }

    public function test_update_falha_com_dados_invalidos(): void
    {
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
        $this->assertDatabaseHas('edificios', ['nome' => 'Edifício A']); // Não alterou
    }

    public function test_destroy_remove_edificio_do_banco(): void
    {
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
        $idInexistente = 9999;

        $this->get(route('edificios.show', $idInexistente))->assertStatus(404);
        $this->get(route('edificios.edit', $idInexistente))->assertStatus(404);
        $this->put(route('edificios.update', $idInexistente), ['nome' => 'x', 'sigla' => 'X'])->assertStatus(404);
        $this->delete(route('edificios.destroy', $idInexistente))->assertStatus(404);
    }
}
