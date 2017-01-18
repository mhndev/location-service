<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dir = '/home/majid/Projects/location-service/.docker-compose/web-server/feed/locations/';
$locations = [];

foreach (glob($dir."*.json") as $filename){

    $data = json_decode(file_get_contents($filename), true);

    $result = [];

    $i = 1;

    foreach ($data as $record) {

        if (empty($record['first'])) {
            if(!empty($record['names'][0]['name'])){

                $record['preview'] = $record['names'][0]['name'];
            }
            else{
                $record['preview'] = $record['names'][2];

            }
        }elseif (empty($record['preview']) && !empty($record['first'])){
            $record['preview'] = $record['first']['names'][0]['name'] . ' - ' .$record['second']['names'][0]['name'];

        }

        if(! empty($record['area'])){
            $record['area'] = $int = intval(preg_replace('/[^0-9]+/', '', $record['area']), 10);;
        }

        if(empty($record['id'])){
            $record['id'] = $i;
        }


        $i++;

        $result[] = $record;
    }

    unlink($filename);


    $text = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    $file = fopen( $filename, 'w');
    fwrite($file, $text);

}
