<?php

namespace App\Http\Controllers;

use App\Models\Edificio;
use App\Models\Horario;
use App\Models\Reserva;
use App\Models\Sala;
use App\Enums\EstadoReserva;
use App\Mail\ReservaCriada;
use App\Mail\ReservaAprovada;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Services\Importador;
use RuntimeException;
use function PHPUnit\Framework\matches;

class ReservaController extends Controller
{

    private function rules(?Reserva $reserva = null): array
    {
        /* Procura a sala com base de dados com base no ID enviado no formulario*/
        $sala = Sala::find(request('sala_id'));

        /* Se a sala existir, usa a capacidade dela. Caso contrário, assume 0.*/
        $capacidadeMaxima = $sala ? $sala->capacidade_maxima : 1;

        return [
            'data' => 'required|date|after_or_equal:today',
            'quantidade_pessoas' => 'required|integer|min:1|max:' . $capacidadeMaxima,
            'tipo_de_uso' => 'required|string|in:SalaDeEstudo,Laboratorio,Leitura,Outro',
            'sala_id' => 'required|integer|exists:salas,id',
            'horarios' => 'required|array|min:1',
            'horarios.*' => 'integer|exists:horarios,id',
            'estado' => ['sometimes', Rule::enum(EstadoReserva::class)],
        ];
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

        $novoEstado =EstadoReserva::from($validated['estado']);

        /* Atualiza o estado */
        $reserva->update(['estado' => $novoEstado]);

        if($novoEstado === EstadoReserva::Confirmada){
            $reserva->load(['utilizador', 'sala']);
            Mail::to($reserva->utilizador->email)->send(new ReservaAprovada($reserva));
        }

        /* Volta para onde o utilizador estava*/
        return back()->with('success', 'Estado da reserva atualizado!');
    }
    public function importarCsv(Request $request, Importador $importador)
    {
        $request->validate([
            'ficheiros'   => ['required', 'array'],
            'ficheiros.*' => ['file', 'mimes:csv,txt'],
        ]);

        $totalImportadas = 0;
        $ficheirosIgnorados = [];

        /* Percorre os ficheiros e tenta abri-los */
        foreach ($request->file('ficheiros') as $ficheiro) {
            try {
                $totalImportadas += $importador->importar($ficheiro->getRealPath());
            } catch (RuntimeException $e) {
                $ficheirosIgnorados[] = $ficheiro->getClientOriginalName() . " ({$e->getMessage()})";
            }
        }

        $msg = "Importação concluída com sucesso: {$totalImportadas} ocupações processadas.";
        if (!empty($ficheirosIgnorados)) {
            $msg .= ' Ignorados: ' . implode(', ', $ficheirosIgnorados);
        }

        return back()->with('success', $msg);
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

        return view('reservas.create', compact('reserva', 'edificios', 'salas', 'horarios', 'dataSelecionada'));
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

        Mail::to($request->user()->email)->send(new ReservaCriada($reserva));

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

        return view('reservas.edit', compact('reserva', 'edificios', 'salas', 'horarios'));
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
