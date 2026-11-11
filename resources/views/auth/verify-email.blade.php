@extends('layouts.app')

@section('content')

    <div>
        <h2>Verifique o seu email</h2>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-link">Clique para reenviar o Email</button>
        </form>
    </div>
@endsection
