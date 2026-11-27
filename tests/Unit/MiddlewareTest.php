<?php

namespace Tests\Unit;

use App\Enums\TipoUtilizador;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\GoogleAuth;
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

    public function test_google_auth_redirects_without_google_session(): void
    {
        $middleware = new GoogleAuth();
        $request = Request::create('/registo', 'GET');
        $request->setLaravelSession($this->app['session']->driver());

        $response = $middleware->handle($request, fn () => new Response('OK', 200));

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(route('login'), $response->getTargetUrl());
    }

    public function test_google_auth_allows_with_google_session(): void
    {
        $middleware = new GoogleAuth();
        $request = Request::create('/registo', 'GET');
        $session = $this->app['session']->driver();
        $session->put('google_registo', [
            'nome' => 'Aluno UFP',
            'email' => 'aluno@ufp.edu.pt',
            'google_id' => 'google-123',
        ]);
        $request->setLaravelSession($session);

        $response = $middleware->handle($request, fn () => new Response('OK', 200));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getContent());
    }
}
