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


$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $globalExceptionHandler = new \mhndev\locationService\exceptions\handler();
        return $globalExceptionHandler->render($exception, $request, $response,$c );

    };
};


$container['db_mysql'] = function($c){
    return function ($c){
        $dsn = "mysql:host=".env('MYSQL_DB_HOST').";dbname=".env('MYSQL_DB_NAME').";charset=".env('MYSQL_DB_CHARSET');

        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $pdo = new PDO($dsn, env('MYSQL_DB_USER'), env('MYSQL_DB_PASS'), $opt);
    };
};



$container['db_elastic'] = function($c){
    $hosts = [ 'host' => env('ELASTIC_DB_HOST'), 'port' => env('ELASTIC_DB_PORT')];
    return $client = Elasticsearch\ClientBuilder::create()
        ->setHosts($hosts)
        ->build();
};


$container['locationRepository'] = function($c){
    return new \mhndev\locationService\services\ElasticSearch($c['db_elastic'], 'digipeyk', 'places');
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
    return new \mhndev\locationService\http\LocationController($c['locationRepository']);
};


$container[\mhndev\locationService\http\UploadDataAction::class] = function ($c) {
    return new \mhndev\locationService\http\UploadDataAction();
};


$container[\mhndev\locationService\http\LocationInputAction::class] = function ($c){
    return new \mhndev\locationService\http\LocationInputAction($c['renderer']);
};
