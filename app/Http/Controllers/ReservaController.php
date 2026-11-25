<?php

namespace App\Http\Controllers;

use App\Models\Edificio;
use App\Models\Horario;
use App\Models\Reserva;
use App\Models\Sala;
use App\Enums\EstadoReserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ReservaController extends Controller
{

    private function rules(?Reserva $reserva = null): array
    {
        /* Procura a sala com base de dados com base no ID enviado no formulario*/
        $sala = Sala::find(request('sala_id'));

        /* Se a sala existir, usa a capacidade dela. Caso contrário, assume 0.*/
        $capacidadeMaxima = $sala ? $sala->capacidade_maxima : 1;

        return [
            'data' => ['required', 'date', 'after_or_equal:today',
                function (string $attribute, mixed $value, \Closure $fail) {
                    try {
                        if (\Carbon\Carbon::parse($value)->isSunday()) {
                            $fail('Não é permitido efetuar reservas ao Domingo.');
                        }
                    } catch (\Throwable) {
                        // O validador 'date' já trata datas inválidas
                    }
                },
            ],
            'quantidade_pessoas' => 'required|integer|min:1|max:' . $capacidadeMaxima,
            'tipo_de_uso' => 'required|string|in:AulaPratica,AulaTeorica,Estudo,Defesa,Palestra,Leitura,Outro,SalaDeEstudo,Laboratorio',
            'sala_id' => 'required|integer|exists:salas,id',
            'horarios' => ['required', 'array', 'min:1', 'max:3',
                function (string $attribute, mixed $value, \Closure $fail) use ($reserva) {
                    if (!is_array($value) || empty($value)) {
                        return;
                    }

                    if (count($value) > 1) {
                        $blocos = Horario::whereIn('id', $value)
                            ->orderBy('hora_inicio')
                            ->get(['id', 'hora_inicio', 'hora_fim']);

                        for ($i = 0; $i < $blocos->count() - 1; $i++) {
                            if ($blocos[$i]->hora_fim !== $blocos[$i + 1]->hora_inicio) {
                                $fail('Os horários selecionados têm de ser consecutivos (máximo 3 blocos seguidos).');
                                return;
                            }
                        }
                    }

                    $salaId = request('sala_id');
                    $data = request('data');

                    if ($salaId && $data) {
                        $conflito = Reserva::where('sala_id', $salaId)
                            ->whereDate('data', $data)
                            ->whereIn('estado', [EstadoReserva::Pendente, EstadoReserva::Confirmada])
                            ->when($reserva?->exists, fn ($q) => $q->where('id', '!=', $reserva->id))
                            ->whereHas('horarios', fn ($q) => $q->whereIn('horarios.id', $value))
                            ->exists();

                        if ($conflito) {
                            $fail('Um ou mais horários selecionados já estão ocupados ou com reserva pendente para esta sala neste dia.');
                        }
                    }
                },
            ],
            'horarios.*' => 'integer|exists:horarios,id',
            'estado' => ['sometimes', Rule::enum(EstadoReserva::class)],
        ];
    }

    private function mapaOcupados(?Reserva $ignorar = null): array
    {
        $reservasAtivas = Reserva::with('horarios:id')
            ->whereIn('estado', [EstadoReserva::Pendente, EstadoReserva::Confirmada])
            ->when($ignorar?->exists, fn ($q) => $q->where('id', '!=', $ignorar->id))
            ->get(['id', 'data', 'sala_id']);

        $mapa = [];
        foreach ($reservasAtivas as $r) {
            $dia = $r->data->format('Y-m-d');
            foreach ($r->horarios as $h) {
                $mapa[$dia][$r->sala_id][] = $h->id;
            }
        }

        return $mapa;
    }

    public function minhasReservas()
    {
        $reservas = Reserva::with(['utilizador', 'sala.edificio', 'horarios'])
            ->where('utilizador_id', Auth::id())->get();
        return view('reservas.index', compact('reservas'));
    }

    public function alterarEstado(Request $request, Reserva $reserva)
    {
        /* verifica se o estado enviado pertence em Enum */
        $validated = $request->validate([
            'estado' => ['required', Rule::enum(EstadoReserva::class)],
        ]);

        /* Atualiza o estado */
        $reserva->update(['estado' => EstadoReserva::from($validated['estado']),]);

        /* Volta para onde o utilizador estava*/
        return back()->with('success', 'Estado da reserva atualizado!');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Reserva::with(['utilizador', 'sala.edificio', 'horarios', 'avaliacao'])
            ->when(!Auth::user()->isAdmin(), fn ($q) => $q->where('utilizador_id', Auth::id()))
            ->when($request->filled('q'), function ($q) use ($request) {
                $termo = '%' . $request->input('q') . '%';
                $q->where(function ($sub) use ($termo) {
                    $sub->where('tipo_de_uso', 'like', $termo)
                        ->orWhereHas('sala', fn ($s) => $s->where('nome_numero', 'like', $termo));
                });
            })
            ->orderByDesc('data');

        $reservas = $query->paginate(10)->withQueryString();
        return view('reservas.index', compact('reservas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $reserva = new Reserva();
        $edificios = Edificio::orderBy('nome')->get();
        $salas = Sala::with('edificio')->orderBy('nome_numero')->get();
        $horarios = Horario::orderBy('hora_inicio')->get();
        $dataParam = $request->query('data');
        $dataSelecionada = ($dataParam && $dataParam >= date('Y-m-d')) ? $dataParam : date('Y-m-d');
        $ocupados = $this->mapaOcupados();

        return view('reservas.create', compact('reserva', 'edificios', 'salas', 'horarios', 'dataSelecionada', 'ocupados'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->rules());

        $reserva = Reserva::create([
            'data' => $validatedData['data'],
            'quantidade_pessoas' => $validatedData['quantidade_pessoas'],
            'tipo_de_uso' => $validatedData['tipo_de_uso'],
            'sala_id'  => $validatedData['sala_id'],
            'utilizador_id'  => Auth::id(),
            'estado' => EstadoReserva::Pendente,
        ]);

        $reserva->horarios()->sync($validatedData['horarios']);

        return redirect()->route('reservas.index')->with('ok', 'Reserva criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reserva $reserva)
    {
        Gate::authorize('view', $reserva);
        $reserva->load(['sala.edificio', 'utilizador', 'horarios', 'avaliacao']);
        return view('reservas.show', compact('reserva'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reserva $reserva)
    {
        Gate::authorize('update', $reserva);
        $reserva->load(['horarios', 'sala']);
        $edificios = Edificio::orderBy('nome')->get();
        $salas = Sala::with('edificio')->orderBy('nome_numero')->get();
        $horarios = Horario::orderBy('hora_inicio')->get();
        $ocupados = $this->mapaOcupados($reserva);

        return view('reservas.edit', compact('reserva', 'edificios', 'salas', 'horarios', 'ocupados'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reserva $reserva)
    {
        Gate::authorize('update', $reserva);
        $validatedData = $request->validate($this->rules($reserva));

        $dados = [
            'data'               => $validatedData['data'],
            'quantidade_pessoas' => $validatedData['quantidade_pessoas'],
            'tipo_de_uso'        => $validatedData['tipo_de_uso'],
            'sala_id'            => $validatedData['sala_id'],
        ];

        if (Auth::user()->isAdmin() && isset($validatedData['estado'])) {
            $dados['estado'] = $validatedData['estado'];
        }

        $reserva->update($dados);
        $reserva->horarios()->sync($validatedData['horarios']);

        return redirect()->route('reservas.index')->with('ok', 'Reserva atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva)
    {
        Gate::authorize('delete', $reserva);
        $reserva->delete();
        return redirect()->route('reservas.index')->with('ok', 'Reserva eliminada com sucesso!');
    }
}
