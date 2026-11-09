<!-- Hora início -->
<div>
    <label for="nome">Hora início</label><br>
    <input type="text" id="horaInicio" name="horaInicio" value="{{ old('horaInicio')}}" placeholder="Hora início">
</div>

<!-- Hora fim -->
<div>
    <label for="nome">Hora fim</label><br>
    <input type="text" id="horaFim" name="horaFim" value="{{ old('horaFim')}}" placeholder="Hora fim">
</div>

<button type="submit">Gravar Horário</button>
