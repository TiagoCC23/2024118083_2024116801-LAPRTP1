<div class="mb-3">
    <label for="reserva_id" class="form-label text-uppercase text-white-50 small">Reserva *</label>
    <select id="reserva_id" name="reserva_id" required class="form-select glass-select">
        <option value="" disabled {{ old('reserva_id', $avaliacao->reserva_id ?? '') ? '' : 'selected' }}>Selecione a Reserva</option>
        @foreach($reservas ?? [] as $reserva)
            <option value="{{ $reserva->id }}" {{ old('reserva_id', $avaliacao->reserva_id ?? '') == $reserva->id ? 'selected' : '' }}>
                #{{ $reserva->id }} — {{ $reserva->sala->nome_numero ?? '' }} ({{ $reserva->data?->format('d/m/Y') }})
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="nota" class="form-label text-uppercase text-white-50 small">Nota (1 a 5) *</label>
    <select id="nota" name="nota" required class="form-select glass-select">
        @for($i = 5; $i >= 1; $i--)
            <option value="{{ $i }}" {{ old('nota', $avaliacao->nota ?? 5) == $i ? 'selected' : '' }}>
                {{ $i }} {{ $i === 1 ? 'Estrela' : 'Estrelas' }}
            </option>
        @endfor
    </select>
</div>

<div class="mb-4">
    <label for="comentario" class="form-label text-uppercase text-white-50 small">Comentário</label>
    <textarea id="comentario" name="comentario" rows="3" maxlength="512"
              placeholder="Partilhe a sua opinião sobre a sala..."
              class="form-control glass-input">{{ old('comentario', $avaliacao->comentario ?? '') }}</textarea>
</div>

<div class="d-flex justify-content-between align-items-center pt-2">
    <a href="{{ route('avaliacoes.index') }}" class="btn btn-glass-secondary">Cancelar</a>
    <button type="submit" class="btn btn-liquid">
        {{ $avaliacao->exists ? 'Atualizar Avaliação' : 'Submeter Avaliação' }}
    </button>
</div>
