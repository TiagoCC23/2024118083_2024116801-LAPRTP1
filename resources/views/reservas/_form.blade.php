<form action="{{ $reserva->exists ? route('reservas.update', $reserva) : route('reservas.store') }}" method="POST">
    @csrf
    @if($reserva->exists)
        @method('PUT')
    @endif

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
        <label class="form-label text-uppercase text-white-50 small d-block mb-1">
            Horários Pretendidos (1 horário ou horários seguidos) *
        </label>
        <small class="text-white-50 d-block mb-2" style="font-size: 0.72rem;">
            Ao escolher um bloco horário, apenas o bloco imediatamente seguinte/anterior pode ser adicionado.
        </small>
        <div class="d-flex flex-wrap gap-2" id="grupo-horarios">
            @php
                $selecionados = array_map('intval', (array) old('horarios', $reserva->exists ? $reserva->horarios->pluck('id')->toArray() : request('horarios', [])));
            @endphp
            @forelse($horarios ?? [] as $idx => $horario)
                <input type="checkbox"
                       class="btn-check horario-checkbox"
                       name="horarios[]"
                       id="h-{{ $horario->id }}"
                       value="{{ $horario->id }}"
                       data-index="{{ $idx }}"
                    {{ in_array($horario->id, $selecionados, true) ? 'checked' : '' }}>
                <label class="btn btn-outline-light rounded-pill px-3 py-2 small" for="h-{{ $horario->id }}">
                    {{ $horario->intervalo }}
                </label>
            @empty
                <span class="text-white-50 small">Nenhum horário disponível.</span>
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = Array.from(document.querySelectorAll('.horario-checkbox'));
            const inputData = document.getElementById('data');

            // impede escolher Domingo no input date
            inputData?.addEventListener('change', function () {
                if (!this.value) return;
                const [y, m, d] = this.value.split('-').map(Number);
                const dataObj = new Date(y, m - 1, d);
                if (dataObj.getDay() === 0) {
                    alert('Não é possível efetuar reservas ao Domingo.');
                    this.value = '';
                }
            });

            function atualizarHorariosAdjacentes() {
                const marcados = checkboxes
                    .filter(cb => cb.checked)
                    .map(cb => parseInt(cb.dataset.index, 10))
                    .sort((a, b) => a - b);

                if (marcados.length === 0) {
                    checkboxes.forEach(cb => {
                        cb.disabled = false;
                        cb.nextElementSibling?.classList.remove('opacity-25');
                    });
                    return;
                }

                const MAX = 3;
                const min = marcados[0];
                const max = marcados[marcados.length - 1];

                checkboxes.forEach(cb => {
                    const idx = parseInt(cb.dataset.index, 10);
                    const podeClicar = cb.checked
                        ? (idx === min || idx === max)
                        : (marcados.length < MAX && (idx === min - 1 || idx === max + 1));

                    if (!cb.checked) {
                        cb.disabled = !podeClicar;
                        cb.nextElementSibling?.classList.toggle('opacity-25', !podeClicar);
                    } else {
                        cb.disabled = false;
                        cb.nextElementSibling?.classList.remove('opacity-25');
                        cb.dataset.lockedMiddle = (!podeClicar) ? '1' : '0';
                    }
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('click', (e) => {
                    if (cb.dataset.lockedMiddle === '1') {
                        e.preventDefault();
                        return;
                    }
                    atualizarHorariosAdjacentes();
                });
            });

            atualizarHorariosAdjacentes();
        });
    </script>
</form>
