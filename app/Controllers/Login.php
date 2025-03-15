<?php
namespace Bdm\TaskManager\Controllers;

use Bdm\TaskManager\Models\User;
use Bdm\TaskManager\System\Controller;
use Bdm\TaskManager\System\Redirect;
use Bdm\TaskManager\System\Route;

class Login extends Controller
{
    private object $userModel;

    public function index(array $data = [], ?string $layout = null): string|false
    {
        return $this->render('layouts/login.php');
    }

    public function onLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            $user = $_POST['user'];
            $pass = $_POST['pass'];

            $this->validateFieldRequired($user, $pass);
            $this->validateLogin($user, $pass);
            $this->createSession();

            Redirect::to('/');
        } catch (\Throwable $th) {
            Redirect::to('/login', [
                'error' => $th->getMessage()
            ]);
        }
    }

    private function validateFieldRequired(string $user, string $pass): void
    {
        if (empty($user)) {
            throw new \Exception("Field user is required!");
        }

        if (empty($pass)) {
            throw new \Exception("Field password is required!");
        }
    }

    private function validateLogin(string $user, string $pass): void
    {
        $userModel = (new User)->where('login', '=', $user)->first();

        if (!$userModel) {
            throw new \Exception("User was not found");
        }

        if (!password_verify($pass, $userModel->password)) {
            throw new \Exception("Incorrect Password");
        }

        $this->userModel = $userModel;
    }

    private function createSession(): void
    {
        $_SESSION['userLogged'] = true;
        $_SESSION['userId'] = $this->userModel->id;
    }

    #[Route('/logout', 'GET')]
    public function logout(): void
    {
        session_start();
        session_unset();
        session_destroy();
        Redirect::to('/login');
    }
}