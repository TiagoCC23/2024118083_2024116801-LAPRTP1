<!doctype html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Criar um edifício</title>
</head>
<body>
<h1>Adicionar Novo Edifício</h1>
<form action="{{ route('edificio.store') }}" method="POST">
    @csrf                               <!--Impede ataques crossfire -->
    <!-- Nome do Edifício -->
    <div>
        <label for="nome">Nome do Edifício</label><br>
        <input type="text" id="nome" name="nome" placeholder="Nome">
    </div>

    <!-- Sigla do Edifício -->
    <div>
        <label for="nome">Sigla</label><br>
        <input type="text" id="sigla" name="sigla" placeholder="Sigla">
    </div>

    <!-- Planta/Imagem do edifício -->
    <div>
        <label for="nome">Nome da imagem</label><br>
        <input type="text" id="imagem" name="imagem" placeholder="Nome do ficheiro da imagem">
    </div>

    <button type="submit">Gravar Edifício</button>
</form>
<br>
<a href="{{ route('edificios.index') }}">Cancelar e Voltar à Lista</a>
</body>
</html>
