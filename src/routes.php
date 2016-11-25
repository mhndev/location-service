<?php
// Routes
/*
$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});*/

$app->get('/geocode','mhndev\locationService\http\LocationController:geocode');
//$app->get('/geocode-google','mhndev\locationService\http\LocationController:geocodeGoogle');



//$app->get('/reverse-google','mhndev\locationService\http\LocationController:reverseGoogle');
$app->get('/reverse','mhndev\locationService\http\LocationController:reverse');


$app->get('/estimate','mhndev\locationService\http\LocationController:estimate');
$app->get('/suggest','mhndev\locationService\http\LocationController:suggest');
