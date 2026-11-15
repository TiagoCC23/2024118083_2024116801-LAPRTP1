<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label for="hora_inicio" class="form-label text-uppercase text-white-50 small">Hora Início *</label>
        <input type="time" id="hora_inicio" name="hora_inicio"
               value="{{ old('hora_inicio', isset($horario->hora_inicio) ? substr($horario->hora_inicio, 0, 5) : '') }}"
               required class="form-control glass-input">
    </div>
    <div class="col-md-6">
        <label for="hora_fim" class="form-label text-uppercase text-white-50 small">Hora Fim *</label>
        <input type="time" id="hora_fim" name="hora_fim"
               value="{{ old('hora_fim', isset($horario->hora_fim) ? substr($horario->hora_fim, 0, 5) : '') }}"
               required class="form-control glass-input">
    </div>
</div>

<div class="d-flex justify-content-between align-items-center pt-2">
    <a href="{{ route('horarios.index') }}" class="btn btn-glass-secondary">Cancelar</a>
    <button type="submit" class="btn btn-liquid">
        {{ $horario->exists ? 'Atualizar Horário' : 'Gravar Horário' }}
    </button>
</div>
