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
use Frgmnt\Controller\AuthController;
use Frgmnt\Controller\PageController;
use Frgmnt\Controller\SiteController;
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
class Routing
{
    private Request $request;
    private Response $response;
    private array $routes = [];

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->defineRoutes();
    }

    /**
     * Define all application routes.
     */
    private function defineRoutes(): void
    {
        // Public site
        $this->addRoute('GET', '/', [SiteController::class, 'startAction']);

        // Authentication
        $this->addRoute('GET', '/core', [AuthController::class, 'loginAction']);
        $this->addRoute('POST', '/core', [AuthController::class, 'loginAction']);

        // Page management in admin
        $this->addRoute('GET', '/core/pages', [PageController::class, 'listAction']);
        $this->addRoute('GET', '/core/pages/edit', [PageController::class, 'editAction']);
        $this->addRoute('POST', '/core/pages/save', [PageController::class, 'saveAction']);

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
            if ($p->getParent_id() && isset($lookup[$p->getParent_id()])) {
                $lookup[$p->getParent_id()]->children[] = $p;
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
                $this->addRoute('GET', '/' . $node->slug, [SiteController::class, 'showAction']);
                $this->addPageRoutesRecursive($node->children, '/' . $node->slug);
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
    public function route(): void
    {
        $method = $this->request->getMethod();
        $path = rtrim($this->request->getUri(), '/') ?: '/';

        if (isset($this->routes[$method][$path])) {
            [$controllerClass, $action] = $this->routes[$method][$path];
            $controller = new $controllerClass($this->request, $this->response);
            $controller->$action($this->request, $this->response);
        } else {
            $this->response->setStatus(404);
            $this->response->write('404 Not Found');
        }
    }
}
