<?php

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

/*
$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});*/

$app->get('/', function(Slim\Http\Request $request, Slim\Http\Response $response){

    return $response->withJson([
        'status' => 200 ,
        'message' => 'welcome, Location Service homepage'
    ], 200);

})->setName('Home');



$app->get('/geocode','mhndev\locationService\http\LocationController:geocode');
$app->get('/geocode-google','mhndev\locationService\http\LocationController:geocodeGoogle');
$app->get('/geocode-quest','mhndev\locationService\http\LocationController:geocodeMapQuest');



$app->get('/reverse-mapquest','mhndev\locationService\http\LocationController:reverseMapQuest');
$app->get('/reverse-google','mhndev\locationService\http\LocationController:reverseGoogle');
$app->get('/reverse','mhndev\locationService\http\LocationController:reverse');


$app->get('/is-in/polygon','mhndev\locationService\http\PointInPolygonAction:__invoke');


$app->get('/suggest','mhndev\locationService\http\LocationController:suggest');

$app->get('/estimate','mhndev\locationService\http\LocationController:estimate');

$app->post('/upload/location-data','mhndev\locationService\http\UploadDataAction:__invoke');
$app->get('/upload/locaation-data/a3eilm2s2y20','mhndev\locationService\http\LocationInputAction:__invoke');
