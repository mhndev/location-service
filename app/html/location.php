<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

define('ROOT', dirname(pathinfo(__FILE__)['dirname']));

define('ROOT_DOCKER', dirname(ROOT) );


define('TEMPLATE_PATH', ROOT . DIRECTORY_SEPARATOR . 'templates');


define('APP_PATH',
    ROOT.
    DIRECTORY_SEPARATOR.
    'src'.
    DIRECTORY_SEPARATOR.
    'app'
);

define('PUBLIC_DIR_PATH',
    ROOT.
    DIRECTORY_SEPARATOR.
    'html'
);


define('EXCEL_DATA_PATH',
    ROOT.
    DIRECTORY_SEPARATOR.
    'storage'.
    DIRECTORY_SEPARATOR.
    'excel'
);


if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: OPTIONS, GET, POST ,PUT, DELETE, PATCH");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();

$uploadStorageConfig = [
    'formats'=>[
        'text' => [
            'location_data'=> [
                'storagePath'     => EXCEL_DATA_PATH.DIRECTORY_SEPARATOR.'location_data',
                'uploadSizeLimit' => 3,
                'cacheDirectory'  => PUBLIC_DIR_PATH.DIRECTORY_SEPARATOR.'excel'.DIRECTORY_SEPARATOR.'location_data'
            ]
        ]
    ],

    'min_storage' => 100
];


\mhndev\media\UploadFile::config($uploadStorageConfig);
