<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$c['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {

        $globalExceptionHandler = new \mhndev\locationService\exception\handler();
        return $globalExceptionHandler->render($exception, $response,$c );

    };
};

$container['authorizationMiddleware'] = function($c){
    return new \mhndev\locationService\middlewares\MiddlewareAuthorization($c);
};

$container['corsMiddleware'] = function($c){
    return new \Tuupola\Middleware\Cors([
        "origin" => ["*"],
        "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
        "headers.allow" => ["Authorization", "If-Match", "If-Unmodified-Since"],
        "headers.expose" => ["Etag"],
        "credentials" => true,
        "cache" => 86400
    ]);
};



$container[\mhndev\locationService\http\LocationController::class] = function ($c) {
    return new \mhndev\locationService\http\LocationController();
};
