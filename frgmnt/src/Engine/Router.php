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


namespace Frgmnt\Engine;

use Frgmnt\Http\Request;
use Frgmnt\Http\Response;
use Frgmnt\Controller\SiteController;
use Frgmnt\Controller\AuthController;
use Frgmnt\Controller\PageController;
use Frgmnt\Container;
use Frgmnt\Repository\PageRepository;

/**
 * Router dispatches HTTP requests to controller actions.
 *
 * Static routes are defined explicitly, and dynamic page routes
 * are built based on the page hierarchy from the database.
 */
class Router
{
    private Request $request;
    private Response $response;
    private Container $container;
    private array $routes = [];

    /**
     * Construct the router with dependencies.
     *
     * @param Request   $request   The HTTP request object
     * @param Response  $response  The HTTP response handler
     * @param Container $container The DI container for services
     */
    public function __construct(Request $request, Response $response, Container $container)
    {
        $this->request = $request;
        $this->response = $response;
        $this->container = $container;
    }

    /**
     * Define all static and dynamic routes for the application.
     *
     * @return void
     */
    public function defineRoutes(): void
    {
        // Public site routes
        $this->addRoute('GET', '/', [SiteController::class, 'startAction']);

        // Authentication routes
        $this->addRoute('GET', '/frgmnt', [AuthController::class, 'loginAction']);
        $this->addRoute('POST', '/frgmnt', [AuthController::class, 'loginAction']);

        // Backend page management
        $this->addRoute('GET', '/frgmnt/pages', [PageController::class, 'listAction']);
        $this->addRoute('GET', '/frgmnt/pages/edit', [PageController::class, 'editAction']);
        $this->addRoute('POST', '/frgmnt/pages/save', [PageController::class, 'saveAction']);

        // Dynamic content pages from database
        $this->defineDynamicPageRoutes();
    }

    /**
     * Register a single route and handler.
     *
     * @param string $method  HTTP method (GET, POST, etc.)
     * @param string $path    URI path to match
     * @param array  $handler [ControllerClass::class, 'actionName']
     *
     * @return void
     */
    private function addRoute(string $method, string $path, array $handler): void
    {
        $key = rtrim($path, '/') ?: '/';
        $this->routes[$method][$key] = $handler;
    }

    /**
     * Build dynamic routes based on the page hierarchy.
     *
     * Fetches all pages, constructs a tree of parent/child relations,
     * and registers a GET route for each node's slug path.
     *
     * @return void
     */
    private function defineDynamicPageRoutes(): void
    {
        $repo = $this->container->get('pageRepo');
        $pages = $repo->fetchAll();

        // Build lookup map and clear children
        $lookup = [];
        foreach ($pages as $p) {
            $p->clearChildren();
            $lookup[$p->getId()] = $p;
        }

        // Attach children to their parents
        $tree = [];
        foreach ($lookup as $p) {
            if ($p->getParentId() !== null && isset($lookup[$p->getParentId()])) {
                $lookup[$p->getParentId()]->addChild($p);
            } else {
                $tree[] = $p;
            }
        }

        // Recursively register routes for the tree
        foreach ($tree as $node) {
            if ($node->getSlug() === 'home') {
                $this->addPageRoutesRecursive($node->getChildren(), '');
            } else {
                $this->addPageRoutesRecursive([$node], '');
            }
        }
    }

    /**
     * Recursively register GET routes for each page node.
     *
     * @param array  $nodes  List of Page nodes to register
     * @param string $prefix URI prefix based on parent path
     *
     * @return void
     */
    private function addPageRoutesRecursive(array $nodes, string $prefix): void
    {
        foreach ($nodes as $node) {
            $path = $prefix . '/' . $node->getSlug();
            $this->addRoute('GET', $path, [SiteController::class, 'showAction']);
            if ($node->getChildren()) {
                $this->addPageRoutesRecursive($node->getChildren(), $path);
            }
        }
    }

    /**
     * Match the incoming request and invoke the appropriate controller action.
     *
     * @return void
     */
    public function dispatch(): void
    {
        $method = $this->request->getMethod();

        $uri = $this->request->getUri();
        $path = parse_url($uri, PHP_URL_PATH);
        $path = rtrim($path, '/') ?: '/';

        if (isset($this->routes[$method][$path])) {
            [$class, $action] = $this->routes[$method][$path];

            // Instantiate controller via Dependency Injection
            $controller = new $class(
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