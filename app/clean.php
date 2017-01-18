<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dir = '/home/majid/Projects/location-service/.docker-compose/web-server/data/locations/';
$locations = [];

foreach (glob($dir."*.json") as $filename){

    $data = json_decode(file_get_contents($filename), true);

    $result = [];

    $i = 1;

    foreach ($data as $record){

        if(!empty($record['id'])){
            unset($record['id']);
        }

        $record['id'] = $i;

        $i++;

        $result[] = $record;
    }

    unlink($filename);


    $text = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    $file = fopen( $filename, 'w');
    fwrite($file, $text);

}
