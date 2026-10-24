<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Salas UFP</title>

</head>
<body>
<!-- Menu de navegação -->
<nav>
    <a href="{{ route ('edificios.index') }}">Edifícios</a> |
    a href="{{ route ('salas.index')}}">Salas</a> |
    a href="{{ route ('reservas.index')}}">Reservas</a> |
    a href="{{ route ('horarios.index')}}">Horários</a> |
    <a href="{{ route ('avaliacoes.index')}}">Avaliações</a>
</nav>

<!-- Área principal -->
<main>

    @yield('content')

</main>
</body>
</html>
