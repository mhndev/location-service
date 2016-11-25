<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$app->add($app->getContainer()->get('corsMiddleware'));
