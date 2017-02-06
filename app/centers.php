<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dir = '/home/majid/Projects/location-service/.docker-compose/web-server/feed/';
$file = $dir.'locations/malls.json';



$data = json_decode(file_get_contents($file), true);

$result = [];


foreach ($data as $record){

    $preview = $record['preview'];

    $search = explode(',',$record['search']);

    $finglish = 'Markaz kharid '.$search[0];

    $record['search'] = implode(',',array_merge($search, [$preview, $finglish]));


    $result[] = $record;

}


$text = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
$file = fopen( $dir.'locations/malls2.json', 'w');
fwrite($file, $text);
