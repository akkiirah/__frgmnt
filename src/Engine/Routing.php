<?php

namespace Frgmnt\Engine;

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
    protected string $controller = '';
    protected string $action = '';
    protected array $params = [];

    /**
     * Initializes the controller, action, and parameters from the HTTP request.
     *
     * Uses GET parameters for the controller and action names.
     * Assigns POST data to parameters if the request method is POST; otherwise, parses the GET parameter params.
     *
     * @return void
     */
    public function __construct()
    {
        $this->controller = isset($_GET['controller']) ? $_GET['controller'] : 'Site';
        $this->action = isset($_GET['action']) ? $_GET['action'] : 'Start';
        $this->params = ($_SERVER['REQUEST_METHOD'] === 'POST')
            ? $_POST
            : (isset($_GET['params']) ? $this->parseParams($_GET['params']) : []);
    }

    /**
     * Dispatches the HTTP request to the designated controller and action.
     *
     * Constructs the controller class name and action method name,
     * instantiates the controller, and invokes the action method with the parsed parameters.
     * Outputs an error message if the controller or action does not exist.
     *
     * @return void
     */
    public function route(): void
    {
        $controllerClass = 'Frgmnt\Controller\\' . $this->controller . 'Controller';
        $actionMethod = $this->action . 'Action';

        if (class_exists($controllerClass)) {
            if (method_exists($controllerClass, $actionMethod)) {
                $controllerInstance = new $controllerClass();
                $controllerInstance->$actionMethod($this->params);
            } else {
                echo 'Oops, method ' . $actionMethod . ' does not exist';
            }
        } else {
            echo 'Oops, controller ' . $controllerClass . ' does not exist';
        }
    }

    /**
     * Parses a parameter string into an associative array.
     *
     * Converts a string formatted as "{key:value1-value2, key2:value3-value4, ...}" into an array.
     * Each key is associated with an array of values; numeric strings are cast to integers.
     *
     * @param string $paramsStr Parameter string to parse.
     * @return array<string, array<int, string|int>> Parsed parameters.
     */
    private function parseParams($paramsStr): array
    {
        $paramsStr = trim($paramsStr, '[]');
        $result = [];
        $pairs = explode(',', $paramsStr);
        foreach ($pairs as $pair) {
            $pair = trim($pair);
            if (empty($pair)) {
                continue;
            }
            $parts = explode(':', $pair, 2);
            if (count($parts) < 2) {
                continue;
            }
            $key = trim($parts[0]);
            $valueStr = trim($parts[1]);
            $values = explode('-', $valueStr);
            $values = array_map(function ($value) {
                $value = trim($value);
                return (filter_var($value, FILTER_VALIDATE_INT) !== false) ? (int) $value : $value;
            }, $values);
            $result[$key] = $values;
        }
        return $result;
    }
}