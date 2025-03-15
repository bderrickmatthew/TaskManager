<?php
use Bdm\TaskManager\System\App;

\session_start();

require __DIR__ . '/vendor/autoload.php';


$app = App::instance();
echo $app->run();