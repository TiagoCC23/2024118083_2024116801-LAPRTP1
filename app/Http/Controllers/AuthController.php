<?php

namespace App\Http\Controllers;

use App\Enums\TipoUtilizador;
use App\Models\Utilizador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller{

    public function showLogin(){
        return view('auth.auth', ['tab' => 'login']);
    }
    public function showRegisto()
    {
        return view('auth.auth', ['tab' => 'registo']);
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
        return redirect()->route('login');
    }
    public function signin(Request $request)
    {
        $googleData = $request->session()->get('google_registo');
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            /*'email' => ['required', 'email', 'max:255', Rule::unique('utilizadores', 'email')], Só para testes depois apago*/
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            /*'tipo_user' => ['required', 'in:estudante,normal'],*//* Os admins são criados no seeder/painel. */
        ]);
        $utilizador = Utilizador::create([
            'nome' => $dados['nome'],
            'email' => $googleData['email'],
            'google_id'=> $googleData['google_id'],
            'password_hash' => Hash::make($dados['password']), /* faz o hash */
            'verificado_em' =>now(),
            /*'tipo_user' => TipoUtilizador::Estudante, depois eu apago ou não*/
        ]);
        $request->session()->forget('google_registo');
        Auth::login($utilizador);
        $request->session()->regenerate(); /*Regenera a sessao apos a criacao de conta*/
        return redirect()->route('reservas.index');
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

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->with(['hd' =>'ufp.edu.pt'])->redirect();
    }

    public function handleGoogleCallback()
    {
        try{
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e){
            return redirect()->route('login')->withErrors(['email'=>'Não foi possível autenticar com a conta Google. Erro: '.$e->getMessage()]);
        }
        $email = strtolower($googleUser->getEmail());
        if(! str_ends_with($email, '@ufp.edu.pt')){
            return redirect()->route('login')
                ->withErrors(['email' => 'Apenas são permitidas contas institucionais (@ufp.edu.pt).']);
        }
        $utilizador = Utilizador::where('email', $email)->first();
        if($utilizador){
            if (!$utilizador->google_id){
                $utilizador->update([
                    'google_id' => $googleUser->getId(),
                    'verificado_em' =>$utilizador->verificado_em ?? now(),
                ]);
            }
            Auth::login($utilizador);
            request()->session()->regenerate();
            return redirect()->intended(route('reservas.index'));
        }
        session([
            'google_registo'=>[
                'nome' =>$googleUser->getName(),
                'email' => $email,
                'google_id' => $googleUser->getId(),
            ],
        ]);
        return redirect()->route('register');
    }

}

