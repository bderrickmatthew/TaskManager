<?php
namespace Bdm\TaskManager\Controllers;

use Bdm\TaskManager\Models\User;
use Bdm\TaskManager\System\Controller;
use Bdm\TaskManager\System\Redirect;
use Bdm\TaskManager\System\Route;
use Bdm\TaskManager\System\CSRF;

class RegisterUsers extends Controller
{
    private object $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User;
    }

    #[Route('/register', 'GET')]
    public function index(array $data = [], ?string $layout = null): string|false
    {
        return $this->render('layouts/register.php');
    }

    #[Route('/register/submit', 'POST')]
    public function onRegister(): void
    {

        try {
            $this->validateFieldRequired();
            $payload = $this->mappingToSave();
            $this->userModel->create($payload);

            CSRF::removeToken();
            Redirect::to('/login', [
                'success' => 'Registration successful. Please log in.'
            ]);
        } catch (\Throwable $th) {
            CSRF::removeToken();
            Redirect::to('/register', [
                'error' => $th->getMessage()
            ]);
        }
    }


    #[Route('/api/v1/users', 'POST')]
    public function onCreateUser(): ?string
    {
        try {
            $this->validateFieldRequired();
            $payload = $this->mappingToSave();
            $this->userModel->create($payload);

            return $this->responseJson([
                'message' => 'Successful registration'
            ]);
        } catch (\Throwable $th) {
            return $this->responseJson(
                data: [
                    'error' => $th->getMessage(),
                ],
                statusCode: 401
            );
        }
    }

    private function validateFieldRequired(): void
    {
        $requiredFields = ['name', 'login', 'password'];

        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new \InvalidArgumentException("The {$field} field is mandatory");
            }
        }
    }

    private function mappingToSave(): array
    {
        return [
            'name' => $_POST['name'],
            'login' => $_POST['login'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ];
    }
}