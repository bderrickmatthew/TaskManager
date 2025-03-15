<?php
namespace Bdm\TaskManager\System;

use Bdm\TaskManager\Traits\Singleton;
use Bdm\TaskManager\Middleware\Authentication;

class App
{
    use Singleton;

    protected $middlewares = [
        Authentication::class,
        //... other middleware you can add in the future
    ];

    public function run(): mixed
    {
        $router = Router::instance();

        // if not route via api apply global middleware
        if (!$router->isApiRoute($router->getCurrentPath())) {
            $this->runMiddlewares();
        }

        return $router->run();
    }

    private function runMiddlewares()
    {
        foreach ($this->middlewares as $middlewareClass) {
            $middleware = new $middlewareClass();
            $middleware->handle();
        }
    }
}