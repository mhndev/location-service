<?php
include 'vendor/autoload.php';

use Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()->build();
//$deleteParams = ['index' => 'digipeyk'];

$repository = new \mhndev\locationService\services\ElasticSearch($client);

try{

    exec('curl -XDELETE localhost:9200/digipeyk/location');
    exec('curl -XDELETE localhost:9200/digipeyk/place');

    exec('curl -XDELETE localhost:9200/digipeyk');


    //$repository->deleteIndex('digipeyk');
}catch (Elasticsearch\Common\Exceptions\Missing404Exception $exception){
    // do nothing
}

exec('
    curl -XPUT localhost:9200/digipeyk -d \'{
        "settings" : {
            "number_of_shards" : 1
        },
        "mappings" : {
            "place" : {
                "properties" : {
                    "place" : { "type" : "geo_point"}
                }
            }
        }
    }\'
');

$intersections = json_decode(file_get_contents('../data/locations/tehran_intersection.json'), true);
$squares = json_decode(file_get_contents('../data/locations/tehran_squares.json'), true);


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
