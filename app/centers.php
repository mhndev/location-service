<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dirData = '/home/majid/Projects/location-service/.docker-compose/web-server/feed/data/';
$dirLocations = '/home/majid/Projects/location-service/.docker-compose/web-server/feed/locations/';


foreach (glob($dirData."*.json") as $filename){


    $data = json_decode(file_get_contents($filename), true);


    $result = [];

    foreach ($data as $record){
        if(!in_array($record, $result)){


            $record['location']['lat'] = floatval($record['location']['lat']);
            $record['location']['lon'] = floatval($record['location']['lon']);

            $result[] = $record;
        }
    }

    $text = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    $file = fopen( $dirLocations.basename($filename), 'w');
    fwrite($file, $text);

}
