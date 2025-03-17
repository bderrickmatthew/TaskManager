<?php
namespace Bdm\TaskManager\Middleware;

use Bdm\TaskManager\System\Redirect;
use Bdm\TaskManager\System\Router;

class Authentication
{
    private $unauthenticatedRoutes = [
        '/login',
        '/login/submit',
        '/register',
        '/register/submit',
    ];

    public function handle()
    {
        $router = Router::instance();

        if (in_array($router->getCurrentPath(), $this->unauthenticatedRoutes)) {
            return;
        }

        // Check if the user is logged in
        if (!isset($_SESSION['userLogged'])) {
            return Redirect::to('/login');
        }
    }
}