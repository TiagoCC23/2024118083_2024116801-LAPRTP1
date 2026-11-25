<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GoogleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request -> session()->has('google_registo')){ /* Se não existir autenticação pelo Google, ele redireciona para a página de login*/
            return redirect()->route('login')->withErrors(['email'=> 'É necessário autenticar primeiro pelo Google' ]);
        }
        return $next($request);
    }
}
