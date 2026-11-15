<form action="{{ isset($reserva) ? route('reservas.update', $reserva) : route('reservas.store') }}" method="POST">
    @csrf
    @if(isset($reserva))
        @method('PUT')
    @endif

    <input type="hidden" name="data" value="{{ old('data', $reserva->data?->format('Y-m-d') ?? $dataString ?? date('Y-m-d')) }}">

    <div class="mb-3">
        <label for="edificio_id" class="form-label text-uppercase text-white-50 small">
            Edifício *
        </label>
        <select id="edificio_id" name="edificio_id" required class="form-select glass-select">
            <option value="" disabled {{ old('edificio_id') ? '' : 'selected' }}>Selecione o Edifício</option>
            @foreach($edificios ?? [] as $edificio)
                <option value="{{ $edificio->id }}" {{ old('edificio_id', $reserva->sala->edificio_id ?? '') == $edificio->id ? 'selected' : '' }}>
                    {{ $edificio->nome }} ({{ $edificio->sigla }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="sala_id" class="form-label text-uppercase text-white-50 small">
            ID da Sala Pretendida *
        </label>
        <input type="number" id="sala_id" name="sala_id" value="{{ old('sala_id', $reserva->sala_id ?? '') }}" required placeholder="Ex: 1" class="form-control glass-input">
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="tipo_de_uso" class="form-label text-uppercase text-white-50 small">
                Tipo de Uso *
            </label>
            <input type="text" id="tipo_de_uso" name="tipo_de_uso" value="{{ old('tipo_de_uso', $reserva->tipo_de_uso ?? '') }}" required placeholder="Ex: Estudo Individual" class="form-control glass-input">
        </div>
        <div class="col-md-6">
            <label for="quantidade_pessoas" class="form-label text-uppercase text-white-50 small">
                Nº Pessoas *
            </label>
            <input type="number" id="quantidade_pessoas" name="quantidade_pessoas" min="1" max="50" value="{{ old('quantidade_pessoas', $reserva->quantidade_pessoas ?? 1) }}" required class="form-control glass-input">
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label text-uppercase text-white-50 small d-block mb-2">
            Horários Pretendidos (Seleção Múltipla) *
        </label>
        <div class="d-flex flex-wrap gap-2">
            @php
                $selecionados = old('horarios', isset($reserva) ? $reserva->horarios->pluck('id')->toArray() : []);
            @endphp
            @forelse($horarios ?? [] as $horario)
                <input type="checkbox" class="btn-check" name="horarios[]" id="h-{{ $horario->id }}" value="{{ $horario->id }}" {{ in_array($horario->id, $selecionados) ? 'checked' : '' }}>
                <label class="btn btn-outline-light rounded-pill px-3 py-2 small" for="h-{{ $horario->id }}">
                    {{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fim, 0, 5) }}
                </label>
            @empty
                <span class="text-white-50 small">Nenhum horário disponível.</span>
            @endforelse
        </div>
    </div>

    <div class="pt-2 text-end">
        <button type="submit" class="btn btn-liquid">
            <i class="bi bi-check2-circle me-1"></i> {{ isset($reserva) ? 'Atualizar Reserva' : 'Confirmar Reserva' }}
        </button>
    </div>
</form>
