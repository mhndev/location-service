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
$app->get('/suggest','mhndev\locationService\http\LocationController:suggest');


$app->get('/reverse-elastic','mhndev\locationService\http\LocationController:reverseGeocode');
