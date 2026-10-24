<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
<form method="POST" action="{{ url('/login') }}">
    @csrf
    <input type="email" name="email" required />
    <input type="password" name="password" required />
    <button type="submit">Entrar</button>
</form>
</body>
</html>
