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
            'nome' => 'Edifício Principal',
            'sigla' => 'ED01',
            'imagem' => null,
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

    public function test_validacao_falha_ao_criar_edificio_sem_campos_obrigatorios(): void
    {
        $response = $this->post(route('edificios.store'), []);

        $response->assertSessionHasErrors(['nome', 'sigla']);
    }

    public function test_edit_retorna_view_com_o_edificio(): void
    {
        $edificio = Edificio::create([
            'nome' => 'Edifício Secundário',
            'sigla' => 'ED02',
            'imagem' => null,
        ]);

        $response = $this->get(route('edificios.edit', $edificio->id));

        $response->assertStatus(200);
        $response->assertViewIs('edificios.edit');
        $response->assertViewHas('edificios');
    }
}
