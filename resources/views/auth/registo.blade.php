<!DOCTYPE html>
<html>
<head><title>Registo</title></head>
<body>
    <form method="POST" action="{{ url('/registo') }}">
        @csrf
        <input type="text" name="nome" required />
        <input type="email" name="email" required />
        <input type="password" name="password" required />
        <input type="password" name="password_confirmation" required />
        <button type="submit">Criar Conta</button>
    </form>
</body>
</html>
