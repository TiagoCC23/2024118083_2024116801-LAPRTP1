<form action="{{ route('reservas.store') }}" method="POST">
    @csrf

    <input type="hidden" name="data" value="{{ $dataString ?? request('data', date('Y-m-d')) }}">

    <div class="mb-3">
        <label for="edificio_id" class="form-label text-uppercase text-white-50 small" style="font-size: 0.7rem; letter-spacing: 0.1em;">
            Edifício *
        </label>
        <select id="edificio_id" name="edificio_id" required class="form-select glass-select">
            <option value="" disabled selected>Selecione o Edifício</option>
            @foreach($edificios ?? [] as $edificio)
                <option value="{{ $edificio->id }}">{{ $edificio->nome }} ({{ $edificio->sigla }})</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="sala_id" class="form-label text-uppercase text-white-50 small" style="font-size: 0.7rem; letter-spacing: 0.1em;">
            Sala Pretendida *
        </label>
        <input type="text" id="sala_id" name="sala_id" required placeholder="Ex: Sala 204" class="form-control glass-input">
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="tipo_de_uso" class="form-label text-uppercase text-white-50 small" style="font-size: 0.7rem; letter-spacing: 0.1em;">
                Tipo de Uso *
            </label>
            <input type="text" id="tipo_de_uso" name="tipo_de_uso" required placeholder="Ex: Estudo Individual" class="form-control glass-input">
        </div>
        <div class="col-md-6">
            <label for="quantidade_pessoas" class="form-label text-uppercase text-white-50 small" style="font-size: 0.7rem; letter-spacing: 0.1em;">
                Nº Pessoas *
            </label>
            <input type="number" id="quantidade_pessoas" name="quantidade_pessoas" min="1" max="50" value="1" required class="form-control glass-input">
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label text-uppercase text-white-50 small d-block mb-2" style="font-size: 0.7rem; letter-spacing: 0.1em;">
            Horários Pretendidos *
        </label>
        <div class="d-flex flex-wrap gap-2">
            @forelse($horarios ?? [] as $horario)
                @php
                    $inicio = substr($horario->hora_inicio, 0, 5);
                    $fim = substr($horario->hora_fim, 0, 5);
                @endphp
                <input type="checkbox" class="btn-check" name="horarios[]" id="h-{{ $horario->id }}" value="{{ $horario->id }}">
                <label class="btn btn-outline-light rounded-pill px-3 py-2 small" for="h-{{ $horario->id }}">
                    {{ $inicio }} - {{ $fim }}
                </label>
            @empty
                <span class="text-white-50 small">Nenhum horário disponível para seleção.</span>
            @endforelse
        </div>
    </div>

    <div class="pt-2 text-end">
        <button type="submit" class="btn btn-liquid">
            <i class="bi bi-check2-circle me-1"></i> Confirmar Reserva
        </button>
    </div>
</form>
