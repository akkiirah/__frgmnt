<?php

/*
 * Licensed under JNK 1.1 — an anti-capitalist, share-alike license.
 *
 * Freely remix, learn, rebuild — just don’t sell or lock it down.
 * Derivatives must stay free and link back to the source.
 *
 * Full license: https://dstwre.sh/license
 */


namespace Frgmnt\Engine;

use Frgmnt\Http\Request;
use Frgmnt\Http\Response;
use Frgmnt\Container;

use Frgmnt\Controller\SiteController;
use Frgmnt\Controller\AuthController;
use Frgmnt\Controller\PageController;
use Frgmnt\Repository\PageRepository;

/**
 * Routes HTTP requests to the appropriate controller and action.
 *
 * Determines the target controller, action, and parameters from the HTTP request.
 * Instantiates the controller and calls the corresponding action method with the parsed parameters.
 * Outputs error messages if the controller or action does not exist.
 *
 * @package Engine
 */
class Router
{
    private Request $request;
    private Response $response;
    private Container $container;
    private array $routes = [];

    // 1) Konstruktor übernimmt nun Container
    public function __construct(Request $request, Response $response, Container $container)
    {
        $this->request = $request;
        $this->response = $response;
        $this->container = $container;
    }

    /**
     * Define all application routes.
     */
    public function defineRoutes(): void
    {
        // Public site
        $this->addRoute('GET', '/', [SiteController::class, 'startAction']);

        // Authentication
        $this->addRoute('GET', '/frgmnt', [AuthController::class, 'loginAction']);
        $this->addRoute('POST', '/frgmnt', [AuthController::class, 'loginAction']);

        // Page management in admin
        $this->addRoute('GET', '/frgmnt/pages', [PageController::class, 'listAction']);
        $this->addRoute('GET', '/frgmnt/pages/edit', [PageController::class, 'editAction']);
        // $this->addRoute('POST', '/frgmnt/pages/save', [PageController::class, 'saveAction']);

        $this->defineDynamicPageRoutes();
    }

    private function defineDynamicPageRoutes(): void
    {
        $repo = new PageRepository();
        $pages = $repo->fetchAll();

        // 2.1) Aufbau eines ID-basierten Lookup-Trees
        $lookup = [];
        foreach ($pages as $p) {
            $p->children = [];
            $lookup[$p->getId()] = $p;
        }
        $tree = [];
        foreach ($lookup as $p) {
            if ($p->getParentId() && isset($lookup[$p->getParentId()])) {
                $lookup[$p->getParentId()]->children[] = $p;
            } else {
                $tree[] = $p;
            }
        }

        // 2.2) Top-Level durchgehen: Home-Slug überspringen
        foreach ($tree as $node) {
            if ($node->getSlug() === 'home') {
                // Home selbst bleibt unter "/", Kinder starten bei ""
                $this->addPageRoutesRecursive($node->children, '');
            } else {
                // alle anderen Top-Level-Seiten bekommen "/slug"
                $this->addRoute('GET', '/' . $node->getSlug(), [SiteController::class, 'showAction']);
                $this->addPageRoutesRecursive($node->children, '/' . $node->getSlug());
            }
        }
    }

    private function addPageRoutesRecursive(array $nodes, string $prefix): void
    {
        foreach ($nodes as $node) {
            $path = $prefix . '/' . $node->getSlug();
            $this->addRoute('GET', $path, [SiteController::class, 'showAction']);
            if ($node->children) {
                $this->addPageRoutesRecursive($node->children, $path);
            }
        }
    }

    /**
     * Register a new route.
     */
    private function addRoute(string $method, string $path, array $handler): void
    {
        $normalized = rtrim($path, '/') ?: '/';
        $this->routes[$method][$normalized] = $handler;
    }

    /**
     * Dispatch the current request to the matching handler.
     */
    public function dispatch(): void
    {
        $method = $this->request->getMethod();
        $path = rtrim($this->request->getUri(), '/') ?: '/';

        if (isset($this->routes[$method][$path])) {
            [$controllerClass, $action] = $this->routes[$method][$path];
            // Controller jetzt per DI erzeugen
            $controller = new $controllerClass(
                $this->request,
                $this->response,
                $this->container->get('view'),
                $this->container->get('db'),
                $this->container->get('auth')
            );
            $controller->$action();
        } else {
            $this->response->setStatus(404);
            $this->response->write('404 Not Found');
        }
    }
}
