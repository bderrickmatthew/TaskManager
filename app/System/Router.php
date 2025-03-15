<?php
namespace Bdm\TaskManager\System;

use Bdm\TaskManager\Traits\Singleton;
use Bdm\TaskManager\Controllers\RegisterUsers;
use Bdm\TaskManager\Controllers\Login;

class Router
{
    use Singleton;

    private array $routes = [];

    private function init(): void
    {
        $traditionalRoutes = require \getcwd() . '/app/routes.php';
        $attributeRoutes = $this->getRoutesFromAttributes();
        $this->routes = array_merge_recursive($traditionalRoutes, $attributeRoutes);
        // var_dump('Merged Routes:', $this->routes); // Debug merged routes
    }

    public function run(): string|false
    {
        $path = $this->getCurrentPath();
        // var_dump('Current Path:', $path); // Debug current path

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        // var_dump('HTTP Method:', $httpMethod); // Debug HTTP method

        $controllerMapping = $this->routes[$httpMethod][$path] ?? null;
        // var_dump('Controller Mapping:', $controllerMapping); // Debug controller mapping

        if ($controllerMapping) {
            [$controllerClass, $method, $middlewares] = $controllerMapping + [null, null, []];
            // var_dump('Controller Class:', $controllerClass); // Debug controller class
            // var_dump('Method:', $method); // Debug method

            // Ensure $middlewares is always an array BEFORE the foreach loop
            if (!is_array($middlewares)) {
                $middlewares = (array) $middlewares;
            }

            // apply route-specific middleware if they are api routes
            foreach ($middlewares as $middlewareClass) {
                if (class_exists($middlewareClass) && method_exists($middlewareClass, 'handle')) {
                    $middleware = new $middlewareClass();
                    $middleware->handle();
                }
            }

            $controller = new $controllerClass();
            return $controller->$method();
        }
        return View::render('layouts/404.php');
    }

    public function getCurrentPath()
    {
        return \parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function getRoutesFromAttributes(): array
    {
        $controllers = [
            RegisterUsers::class,
            Login::class, // Add this line
        ];

        $routes = [];

        foreach ($controllers as $controller) {
            $reflection = new \ReflectionClass($controller);

            foreach ($reflection->getMethods() as $method) {
                $routeAttribute = $method->getAttributes(Route::class)[0] ?? null;

                if ($routeAttribute) {
                    $httpMethod = $routeAttribute->newInstance()->method;
                    $path = $routeAttribute->newInstance()->path;
                    $middlewares = $routeAttribute->newInstance()->middlewares;

                    // Validate that the middlewares are valid middleware classes
                    $validMiddlewares = [];
                    foreach ($middlewares as $middlewareClass) {
                        if (class_exists($middlewareClass) && method_exists($middlewareClass, 'handle')) {
                            $validMiddlewares[] = $middlewareClass;
                        }
                    }

                    $routes[$httpMethod][$path] = [$controller, $method->getName(), $validMiddlewares];
                }
            }
        }

        return $routes;
    }

    public function isApiRoute(string $path): bool
    {
        return strpos($path, '/api/') === 0;
    }
}