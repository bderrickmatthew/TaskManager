<?php
use Bdm\TaskManager\Controllers\Home;
use Bdm\TaskManager\Controllers\Login;
use Bdm\TaskManager\Controllers\Tasks;
use Bdm\TaskManager\Controllers\RegisterUsers;

return [
    'GET' => [
        '/' => [Home::class, 'index'],
        '/login' => [Login::class, 'index'],
        '/tasks' => [Tasks::class, 'index'],
        '/register' => [RegisterUsers::class, 'index'],
    ],
    'POST' => [
        '/login/submit' => [Login::class, 'onLogin'],
        '/tasks/create' => [Tasks::class, 'onCreateTask'],
        '/tasks/completed' => [Tasks::class, 'onActionTask'],
        '/tasks/delete' => [Tasks::class, 'onActionTask'],
        '/tasks/delete-all' => [Tasks::class, 'onDeleteAll'],
        '/register/submit' => [RegisterUsers::class, 'onRegister'],
    ]
];