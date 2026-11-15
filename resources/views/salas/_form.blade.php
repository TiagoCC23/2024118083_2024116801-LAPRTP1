<div class="mb-3">
    <label for="nome_numero" class="form-label text-uppercase text-white-50 small">Nome / Número da Sala *</label>
    <input type="text" id="nome_numero" name="nome_numero" value="{{ old('nome_numero', $sala->nome_numero ?? '') }}" required
           placeholder="Ex: Sala 302" class="form-control glass-input">
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="capacidade_maxima" class="form-label text-uppercase text-white-50 small">Capacidade Máxima *</label>
        <input type="number" id="capacidade_maxima" name="capacidade_maxima" min="1"
               value="{{ old('capacidade_maxima', $sala->capacidade_maxima ?? 30) }}" required class="form-control glass-input">
    </div>
    <div class="col-md-6">
        <label for="piso" class="form-label text-uppercase text-white-50 small">Piso *</label>
        <input type="number" id="piso" name="piso" value="{{ old('piso', $sala->piso ?? 1) }}" required class="form-control glass-input">
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="tipo_sala" class="form-label text-uppercase text-white-50 small">Tipo de Sala *</label>
        <select id="tipo_sala" name="tipo_sala" required class="form-select glass-select">
            @foreach(['SalaDeAula' => 'Sala de Aula', 'Trabalho' => 'Trabalho', 'Exame' => 'Exame', 'Outro' => 'Outro'] as $val => $label)
                <option value="{{ $val }}" {{ old('tipo_sala', $sala->tipo_sala ?? '') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label for="disponibilidade" class="form-label text-uppercase text-white-50 small">Disponibilidade *</label>
        <select id="disponibilidade" name="disponibilidade" required class="form-select glass-select">
            @foreach($disponibilidades ?? \App\Enums\Disponibilidade::cases() as $disp)
                <option value="{{ $disp->value }}" {{ old('disponibilidade', $sala->disponibilidade ?? 'disponivel') === $disp->value ? 'selected' : '' }}>
                    {{ $disp->label() }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="mb-4">
    <label for="edificio_id" class="form-label text-uppercase text-white-50 small">Edifício *</label>
    <select id="edificio_id" name="edificio_id" required class="form-select glass-select">
        <option value="" disabled {{ old('edificio_id', $sala->edificio_id ?? '') ? '' : 'selected' }}>Selecione o Edifício</option>
        @foreach($edificios ?? [] as $edificio)
            <option value="{{ $edificio->id }}" {{ old('edificio_id', $sala->edificio_id ?? '') == $edificio->id ? 'selected' : '' }}>
                {{ $edificio->nome }} ({{ $edificio->sigla }})
            </option>
        @endforeach
    </select>
</div>

<div class="d-flex justify-content-between align-items-center pt-2">
    <a href="{{ route('salas.index') }}" class="btn btn-glass-secondary">Cancelar</a>
    <button type="submit" class="btn btn-liquid">
        {{ $sala->exists ? 'Atualizar Sala' : 'Criar Sala' }}
    </button>
</div>
