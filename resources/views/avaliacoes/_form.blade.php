<!-- Nome do Edifício -->
<div>
    <label for="nome">Nome do Edifício</label><br>
    <input type="text" id="nome" name="nome" value ="{{ $avaliacao->nome }}" placeholder="Nome">
</div>

<!-- Sigla do Edifício -->
<div>
    <label for="nome">Sigla</label><br>
    <input type="text" id="sigla" name="sigla" value ="{{ $edificio->sigla }}" placeholder="Sigla">
</div>

<!-- Planta/Imagem do edifício -->
<div>
    <label for="nome">Nome da imagem</label><br>
    <input type="text" id="imagem" name="imagem" value ="{{ $edificio->imagem }}" placeholder="Nome do ficheiro da imagem">
</div>

<button type="submit">Atualizar Edifício</button>
