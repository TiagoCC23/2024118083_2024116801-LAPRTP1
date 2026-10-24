<?php

namespace App\Http\Controllers;

use App\Models\Utilizador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller{

    public function showLogin(){
        return view('auth.login');
    }
    public function showRegisto()
    {
        return view('auth.registo');
    }

    public function login(Request $request)
    {
    $dados = $request->validate(['email'=>['required', 'email'],
                                 'password'=>['required'],]);
    if(!Auth::attempt($dados)){ /*Caso ele falhe na autenticaçao, ele volta a onde estavamos e */
        return back()->withErrors(['email' => 'Dados Inválidos.'])->onlyInput('email');
    }
    $request->session()->regenerate(); /* evita que a sessão fique fixa **/
    return redirect()->intended(route('reservas.index'));
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('salas.index');
    }
    public function signin(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('utilizadores', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'tipo_user' => ['required', 'in:estudante,normal'],/* Os admins são criados no seeder/painel. */
        ]);
        $utilizador = Utilizador::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'password_hash' => Hash::make($dados['password']), /* faz o hash */
            'tipo_user' => $dados['tipo_user'],
            'verificado_em' => now(), // TODO E2: trocar por verificação real de email
        ]);
        Auth::login($utilizador);
        $request->session()->regenerate(); /*Regenera a sessao apos a criacao de conta*/
        return redirect()->route('reservas.index')->with('success', 'Conta criada com sucesso');
    }
    public function deleteAccount(Request $request)
    {
    $utilizador = $request->user();
    Auth::logout();
    $utilizador->delete();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('salas.index')->with('success', 'Conta apagada com sucesso');
    }

}

