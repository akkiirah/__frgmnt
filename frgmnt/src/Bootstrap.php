<?php
declare(strict_types=1);

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
use Frgmnt\Service\AuthService;
use Frgmnt\Repository\PageRepository;
use Frgmnt\View\LatteViewRenderer;

/**
 * Bootstrap initializes the application, setting up the DI container and router.
 */
class Bootstrap
{
    private Container $container;
    private Router $router;

    /**
     * Private constructor to configure services and router.
     */
    private function __construct()
    {
        $this->container = new Container();

        // Register shared services
        $this->container->set('request', fn() => new Request());
        $this->container->set('response', fn() => new Response());
        $this->container->set('db', fn() => Database::getConnection());
        $this->container->set('view', fn() => new LatteViewRenderer());
        $this->container->set('auth', fn() => new AuthService());
        $this->container->set('pageRepo', fn() => new PageRepository(
            $this->container->get('db')
        ));

        // Instantiate router with dependencies
        $this->router = new Router(
            $this->container->get('request'),
            $this->container->get('response'),
            $this->container
        );
    }

    /**
     * Create a new Bootstrap instance.
     *
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Run the application: define routes and dispatch.
     *
     * @return void
     */
    public function run(): void
    {
        $this->router->defineRoutes();
        $this->router->dispatch();
    }
}