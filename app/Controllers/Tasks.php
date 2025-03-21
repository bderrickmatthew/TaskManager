<?php
namespace Bdm\TaskManager\Controllers;

use Bdm\TaskManager\Models\Task;
use Bdm\TaskManager\System\Controller;
use Bdm\TaskManager\System\Redirect;
use Exception;

class Tasks extends Controller
{
    public Task $taskModel;

    public function __construct()
    {
        parent::__construct();
        $this->taskModel = new Task;
    }

    public function index(array $data = [], ?string $layout = 'layouts/index.php'): string|false
    {
        return parent::index([
            'tasks' => $this->taskModel->where('user_id', '=', $_SESSION['userId'])->all(),
        ], $layout);
    }

    public function onCreateTask(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            $payload = [
                'title' => ucfirst($_POST['task']),
                'user_id' => $_SESSION['userId'],
                'is_concluded' => 0,
            ];

            $this->taskModel->createTask($payload);

            Redirect::to('/tasks', [
                'success' => 'Successfully created'
            ]);
        } catch (\Throwable $th) {
            Redirect::to('/tasks', [
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function onActionTask(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            if (!method_exists($this->taskModel, $_POST['action'])) {
                throw new Exception("Method does not exist in the Tasks model");
            }

            $this->taskModel->{$_POST['action']}($_POST['id']);

            Redirect::to('/tasks', [
                'success' => 'Task updated successfully'
            ]);
        } catch (\Throwable $th) {
            Redirect::to('/tasks', [
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function onDeleteAll(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            $this->taskModel->clearAllTasks();

            Redirect::to('/tasks', [
                'success' => 'All records deleted successfully'
            ]);
        } catch (\Throwable $th) {
            Redirect::to('/tasks', ['error' => $th->getMessage()]);
        }
    }
}