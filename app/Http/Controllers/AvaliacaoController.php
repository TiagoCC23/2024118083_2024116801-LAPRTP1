<?php

namespace App\Http\Controllers;

use App\Enums\EstadoReserva;
use App\Models\Avaliacao;
use App\Models\Reserva;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AvaliacaoController extends Controller
{
    private const PALAVRAS_PROIBIDAS = [
        'merda', 'porra', 'caralho', 'puta', 'foda', 'foder', 'fodase',
        'cabrao', 'cabra', 'corno', 'idiota', 'imbecil', 'estupido', 'burro',
        'otario', 'babaca', 'arrombado', 'desgracado', 'bosta', 'cu', 'pila', 'cona',
        'fds', 'pqp', 'fdp', 'vagabundo', 'vagabunda',
        'ass', 'cunt', 'dick', 'cock', 'pussy'
    ];

    private function rules(?Avaliacao $avaliacao = null): array
    {
        return [
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => [
                'nullable',
                'string',
                'max:512',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (!is_string($value) || trim($value) === '') {
                        return;
                    }

                    // Normaliza o texto (minúsculas e sem acentos) para detetar variações
                    $textoLimpo = Str::lower(Str::ascii($value));

                    foreach (self::PALAVRAS_PROIBIDAS as $palavra) {
                        if (preg_match('/\b' . preg_quote($palavra, '/') . '\b/u', $textoLimpo)) {
                            $fail('O comentário contém linguagem imprópria. Por favor, utilize termos adequados.');
                            return;
                        }
                    }
                },
            ],
            'reserva_id' => [
                'required',
                'integer',
                'exists:reservas,id',
                Rule::unique('avaliacoes', 'reserva_id')->ignore($avaliacao),
            ],
        ];
    }

    /**
     * Apenas o autor da avaliação (dono da reserva) pode editar o seu comentário.
     * Administradores e outros utilizadores recebem 403.
     */
    private function autorizarEdicao(Avaliacao $avaliacao): void
    {
        $avaliacao->loadMissing('reserva');

        if (!$avaliacao->reserva || $avaliacao->reserva->utilizador_id !== Auth::id()) {
            abort(403, 'Apenas o autor da avaliação pode editar este comentário.');
        }
    }

    /**
     * Apenas o autor da avaliação ou um Administrador podem eliminar uma avaliação.
     */
    private function autorizarEliminacao(Avaliacao $avaliacao): void
    {
        $avaliacao->loadMissing('reserva');
        $user = Auth::user();

        if (!$user?->isAdmin() && $avaliacao->reserva?->utilizador_id !== Auth::id()) {
            abort(403, 'Não tem permissão para eliminar esta avaliação.');
        }
    }

    public function minhasAvaliacoes()
    {
        $userId = Auth::id();

        $avaliacoes = Avaliacao::with(['reserva.sala.edificio', 'reserva.utilizador'])
            ->whereHas('reserva', fn ($query) => $query->where('utilizador_id', $userId))
            ->paginate(10);

        return view('avaliacoes.self', compact('avaliacoes'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $avaliacoes = Avaliacao::with(['reserva.sala.edificio', 'reserva.utilizador'])->paginate(10);
        return view('avaliacoes.index', compact('avaliacoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $avaliacao = new Avaliacao();
        $reservas = Reserva::with('sala.edificio')
            ->where('utilizador_id', Auth::id())
            ->where('estado', EstadoReserva::Confirmada)
            ->whereDoesntHave('avaliacao')
            ->orderByDesc('data')
            ->get();

        return view('avaliacoes.create', compact('avaliacao', 'reservas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate($this->rules());
        Avaliacao::create($validateData);
        return redirect()->route('avaliacoes.index')->with('success', 'Avaliação criada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Avaliacao $avaliacao)
    {
        $avaliacao->load(['reserva.sala.edificio', 'reserva.utilizador']);
        return view('avaliacoes.show', compact('avaliacao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Avaliacao $avaliacao)
    {
        $this->autorizarEdicao($avaliacao);

        $avaliacao->load('reserva.sala.edificio');
        $reservas = Reserva::with('sala.edificio')
            ->where('id', $avaliacao->reserva_id)
            ->get();

        return view('avaliacoes.edit', compact('avaliacao', 'reservas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Avaliacao $avaliacao)
    {
        $this->autorizarEdicao($avaliacao);

        $validateData = $request->validate($this->rules($avaliacao));
        $avaliacao->update($validateData);
        return redirect()->route('avaliacoes.index')->with('success', 'Atualizado com Sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Avaliacao $avaliacao)
    {
        $this->autorizarEliminacao($avaliacao);

        $avaliacao->delete();
        return redirect()->route('avaliacoes.index')->with('success', 'Registo apagado com sucesso');
    }
}
