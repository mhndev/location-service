<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/location.php';


session_start();

$settings = require __DIR__ . '/../src/settings.php';

if(env('DOWN_FOR_MAINTENANCE')){
    include __DIR__ . "/../templates/maintenance.phtml";
    die();
}

$app = new \Slim\App($settings);

require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/dependencies.php';
require __DIR__ . '/../src/middleware.php';
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();
