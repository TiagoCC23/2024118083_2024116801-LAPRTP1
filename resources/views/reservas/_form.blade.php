<form action="{{ $reserva->exists ? route('reservas.update', $reserva) : route('reservas.store') }}" method="POST">
    @csrf
    @if($reserva->exists)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="data" class="form-label text-uppercase text-white-50 small">Data da Reserva *</label>
        <input type="date" id="data" name="data"
               min="{{ date('Y-m-d') }}"
               value="{{ old('data', $reserva->data?->format('Y-m-d') ?? $dataSelecionada ?? date('Y-m-d')) }}"
               required class="form-control glass-input">
    </div>

    <div class="mb-3">
        <label for="sala_id" class="form-label text-uppercase text-white-50 small">Sala Pretendida *</label>
        <select id="sala_id" name="sala_id" required class="form-select glass-select">
            <option value="" disabled {{ old('sala_id', $reserva->sala_id ?? '') ? '' : 'selected' }}>Selecione a Sala</option>
            @foreach($salas ?? [] as $sala)
                <option value="{{ $sala->id }}" {{ old('sala_id', $reserva->sala_id ?? '') == $sala->id ? 'selected' : '' }}>
                    {{ $sala->edificio->sigla ?? '' }} · {{ $sala->nome_numero }} (Máx: {{ $sala->capacidade_maxima }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="tipo_de_uso" class="form-label text-uppercase text-white-50 small">Tipo de Uso *</label>
            <select id="tipo_de_uso" name="tipo_de_uso" required class="form-select glass-select">
                @foreach(['SalaDeEstudo' => 'Sala de Estudo', 'Laboratorio' => 'Laboratório', 'Leitura' => 'Leitura', 'Outro' => 'Outro'] as $val => $label)
                    <option value="{{ $val }}" {{ old('tipo_de_uso', $reserva->tipo_de_uso ?? '') === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="quantidade_pessoas" class="form-label text-uppercase text-white-50 small">Nº Pessoas *</label>
            <input type="number" id="quantidade_pessoas" name="quantidade_pessoas" min="1" max="100"
                   value="{{ old('quantidade_pessoas', $reserva->quantidade_pessoas ?? 1) }}" required class="form-control glass-input">
        </div>
    </div>

    @if($reserva->exists && auth()->user()?->isAdmin())
        <div class="mb-3">
            <label for="estado" class="form-label text-uppercase text-white-50 small">Estado</label>
            <select id="estado" name="estado" class="form-select glass-select">
                @foreach(\App\Enums\EstadoReserva::cases() as $est)
                    <option value="{{ $est->value }}" {{ old('estado', $reserva->estado?->value) === $est->value ? 'selected' : '' }}>
                        {{ $est->label() }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="mb-4">
        <label class="form-label text-uppercase text-white-50 small d-block mb-2">
            Horários Pretendidos (Seleção Múltipla) *
        </label>
        <div class="d-flex flex-wrap gap-2">
            @php
                $selecionados = old('horarios', $reserva->exists ? $reserva->horarios->pluck('id')->toArray() : []);
            @endphp
            @forelse($horarios ?? [] as $horario)
                <input type="checkbox" class="btn-check" name="horarios[]" id="h-{{ $horario->id }}" value="{{ $horario->id }}"
                    {{ in_array($horario->id, $selecionados) ? 'checked' : '' }}>
                <label class="btn btn-outline-light rounded-pill px-3 py-2 small" for="h-{{ $horario->id }}">
                    {{ $horario->intervalo }}
                </label>
            @empty
                <span class="text-white-50 small">Nenhum horário disponível.</span>
            @endforelse
        </div>
    </div>

    <div class="pt-2 d-flex justify-content-between align-items-center">
        <a href="{{ route('reservas.index') }}" class="btn btn-glass-secondary">Cancelar</a>
        <button type="submit" class="btn btn-liquid">
            <i class="bi bi-check2-circle me-1"></i> {{ $reserva->exists ? 'Atualizar Reserva' : 'Confirmar Reserva' }}
        </button>
    </div>
</form>
