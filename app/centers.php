<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dir = '/home/majid/Projects/location-service/.docker-compose/web-server/feed/';
$file = $dir.'locations/intersections.json';



$data = json_decode(file_get_contents($file), true);

$result = [];

$id = 1;
foreach ($data as $record){

    $record['id'] = $id;
    $result[] = $record;

    $id ++;
}


$text = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
$file = fopen( $dir.'locations/intersections.json', 'w');
fwrite($file, $text);
