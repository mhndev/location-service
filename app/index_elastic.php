<?php
include 'vendor/autoload.php';

use Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()->build();
$deleteParams = ['index' => 'digipeyk'];
$response = $client->indices()->delete($deleteParams);

$intersections = json_decode(file_get_contents('data/locations/tehran_intersection.json'), true)['RECORDS'];
$squares = json_decode(file_get_contents('data/locations/tehran_squares.json'), true)['RECORDS'];



$locations = array_merge($intersections, $squares);

$i = 1;

foreach($locations as $location){

    echo $i."\n";

    $params['index'] = 'digipeyk';
    $params['id'] = $location['id'];
    $params['type'] = 'location';
    $params['body'] = $location;

    $response = $client->index($params);

    $i++;
}
