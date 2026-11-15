<div class="mb-3">
    <label for="nome" class="form-label text-uppercase text-white-50 small">Nome do Edifício *</label>
    <input type="text" id="nome" name="nome" value="{{ old('nome', $edificio->nome ?? '') }}" required
           placeholder="Ex: Edifício Sede" class="form-control glass-input">
</div>

<div class="mb-3">
    <label for="sigla" class="form-label text-uppercase text-white-50 small">Sigla *</label>
    <input type="text" id="sigla" name="sigla" maxlength="5" value="{{ old('sigla', $edificio->sigla ?? '') }}" required
           placeholder="Ex: UFP" class="form-control glass-input">
</div>

<div class="mb-4">
    <label for="imagem" class="form-label text-uppercase text-white-50 small">Nome da Imagem</label>
    <input type="text" id="imagem" name="imagem" value="{{ old('imagem', $edificio->imagem ?? '') }}"
           placeholder="Ex: Sede.png" class="form-control glass-input">
</div>

<div class="d-flex justify-content-between align-items-center pt-2">
    <a href="{{ route('edificios.index') }}" class="btn btn-glass-secondary">Cancelar</a>
    <button type="submit" class="btn btn-liquid">
        {{ $edificio->exists ? 'Atualizar Edifício' : 'Criar Edifício' }}
    </button>
</div>
