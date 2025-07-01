<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


namespace Frgmnt;

use Frgmnt\Engine\Router;
use Frgmnt\Http\Request;
use Frgmnt\Http\Response;
use Frgmnt\Service\Database;
use Frgmnt\View\LatteViewRenderer;

class Bootstrap
{
    private Container $container;
    private Router $router;

    private function __construct()
    {
        // 1) Container initialisieren
        $this->container = new Container();

        // 2) Services registrieren
        $this->container->set('request', fn() => new Request());
        $this->container->set('response', fn() => new Response());
        $this->container->set('db', fn() => Database::connect());           // PDO
        $this->container->set('view', fn() => new LatteViewRenderer());          // Latte wrapper
        $this->container->set('auth', fn() => new Service\AuthService());  // Auth‐Logik

        // 3) Router anlegen und mit Container füttern
        $this->router = new Router(
            $this->container->get('request'),
            $this->container->get('response'),
            $this->container
        );
    }

    public static function create(): self
    {
        return new self();
    }

    public function run(): void
    {
        // Routen definieren und dispatchen
        $this->router->defineRoutes();
        $this->router->dispatch();
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}
