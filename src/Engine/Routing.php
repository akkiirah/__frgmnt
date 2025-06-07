<?php

namespace Frgmnt\Engine;

use Frgmnt\Http\Request;
use Frgmnt\Http\Response;
use Frgmnt\Controller\AuthController;
use Frgmnt\Controller\PageController;
use Frgmnt\Controller\SiteController;

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
            $controller->$action();
        } else {
            $this->response->setStatus(404);
            $this->response->write('404 Not Found');
        }
    }
}
