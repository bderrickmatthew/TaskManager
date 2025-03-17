<?php

namespace Bdm\TaskManager\Middleware;

use Bdm\TaskManager\System\CSRF;
use Bdm\TaskManager\System\Router;
use Bdm\TaskManager\System\Redirect;

class CSRFProtection
{
    /**
     * Routes that don't require CSRF verification
     */
    private array $excludedRoutes = [
        '/api/v1/users',     // API routes
        '/login',            // GET login page
        '/register'          // GET register page
    ];

    /**
     * Methods that require CSRF verification
     */
    private array $protectedMethods = ['POST', 'PUT', 'DELETE', 'PATCH'];

    public function handle(): void
    {
        $router = Router::instance();
        $currentPath = $router->getCurrentPath();

        // Skip CSRF check for excluded routes or non-protected methods
        if (
            in_array($currentPath, $this->excludedRoutes) ||
            !in_array($_SERVER['REQUEST_METHOD'], $this->protectedMethods)
        ) {
            return;
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? null)) {
            CSRF::removeToken();

            // If it's an API route, return JSON response
            if ($router->isApiRoute($currentPath)) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['error' => 'CSRF token validation failed']);
                exit;
            }

            // For web routes, redirect to login with error
            Redirect::to('/login', [
                'error' => 'Security validation failed. Please try again.'
            ]);
        }
    }
}