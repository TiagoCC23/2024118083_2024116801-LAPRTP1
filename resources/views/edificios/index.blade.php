<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lista de Edifícios</title>
</head>
<body>
<h1>Lista de edifícios</h1>
<table>
    <tr>
    <th>ID</th>
    <th>Nome</th>
    <th>Sigla</th>
    <th>Imagem</th>
    </tr>

    @foreach($edificios as $edificio)
        <tr>
            <td> {{ $edificio->id }}</td>
            <td> {{ $edificio->nome }}</td>
            <td> {{ $edificio->sigla }}</td>
            <td> {{ $edificio->imagem }}</td>
            <td><button>Editar</button></td> <!--Depois eu tento entender esta sugestão -->
        </tr>
    @endforeach
</table>
<br>
<a href="{{ route('$edificios.create') }}">Adicionar um novo edifíco</a>
</body>
</html>
