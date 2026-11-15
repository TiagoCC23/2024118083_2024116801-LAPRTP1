<?php

namespace Tests\Unit;

use App\Enums\TipoUtilizador;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckEmailVerificado;
use App\Models\Utilizador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    public function test_check_admin_blocks_guest(): void
    {
        $middleware = new CheckAdmin();
        $request = Request::create('/admin', 'GET');
        $request->setUserResolver(fn () => null);

        $this->expectException(HttpException::class);

        try {
            $middleware->handle($request, fn () => new Response('OK', 200));
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
            throw $e;
        }
    }

    public function test_check_admin_blocks_estudante(): void
    {
        $middleware = new CheckAdmin();
        $estudante = new Utilizador(['tipo_user' => TipoUtilizador::Estudante]);

        $request = Request::create('/admin', 'GET');
        $request->setUserResolver(fn () => $estudante);

        $this->expectException(HttpException::class);

        try {
            $middleware->handle($request, fn () => new Response('OK', 200));
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
            throw $e;
        }
    }

    public function test_check_admin_allows_admin(): void
    {
        $middleware = new CheckAdmin();
        $admin = new Utilizador(['tipo_user' => TipoUtilizador::Admin]);

        $request = Request::create('/admin', 'GET');
        $request->setUserResolver(fn () => $admin);

        $response = $middleware->handle($request, fn () => new Response('OK', 200));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getContent());
    }

    public function test_check_email_verificado_redirects_unverified_user(): void
    {
        $middleware = new CheckEmailVerificado();
        $user = new Utilizador(['verificado_em' => null]);

        $request = Request::create('/home', 'GET');
        $request->setUserResolver(fn () => $user);

        $response = $middleware->handle($request, fn () => new Response('OK', 200));

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(route('verification.notice'), $response->getTargetUrl());
    }

    public function test_check_email_verificado_allows_verified_user_and_guest(): void
    {
        $middleware = new CheckEmailVerificado();

        // Utilizador verificado
        $verified = new Utilizador(['verificado_em' => now()]);
        $requestVerified = Request::create('/home', 'GET');
        $requestVerified->setUserResolver(fn () => $verified);

        $responseVerified = $middleware->handle($requestVerified, fn () => new Response('OK', 200));
        $this->assertSame(200, $responseVerified->getStatusCode());

        // Guest (passa para o próximo middleware)
        $requestGuest = Request::create('/home', 'GET');
        $requestGuest->setUserResolver(fn () => null);

        $responseGuest = $middleware->handle($requestGuest, fn () => new Response('OK', 200));
        $this->assertSame(200, $responseGuest->getStatusCode());
    }
}
