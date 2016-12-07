<?php
include 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

use Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()->build();
//$deleteParams = ['index' => 'digipeyk'];

$index = 'digipeyk';
$type = 'places';

$repository = new \mhndev\locationService\services\ElasticSearch($client, $index, $type);

try{
    exec('curl -XDELETE '.env('ELASTIC_DB_HOST').':'.env('ELASTIC_DB_PORT').'/'.$index.'/'.$type);
    exec('curl -XDELETE '.env('ELASTIC_DB_HOST').':'.env('ELASTIC_DB_PORT').'/digipeyk');
    //$repository->deleteIndex('digipeyk');
}catch (Elasticsearch\Common\Exceptions\Missing404Exception $exception){
    // do nothing
}

exec('
    curl -XPUT '.env('ELASTIC_DB_HOST').':'.env('ELASTIC_DB_PORT').'/'.$index.' -d \'{
        "settings" : {
            "number_of_shards" : 1
        },
        "mappings" : {
            "'.$type.'" : {
                "properties" : {
                    "location" : { "type" : "geo_point"}
                }
            }
        }
    }\'
');

$intersections = json_decode(file_get_contents('/docker/data/feed-locations/tehran_intersection.json'), true);
$squares = json_decode(file_get_contents('/docker/data/feed-locations/tehran_squares.json'), true);

$locations = array_merge($intersections, $squares);
$i = 1;

foreach($locations as $location){

    echo $i."\n";

    $location['location'] = ['lat' => $location['latitude'], 'lon' => $location['longitude'] ];

    unset($location['latitude']);
    unset($location['longitude']);

    $params['index'] = $index;
    $params['id'] = $location['id'];
    $params['type'] = $type;
    $params['body'] = $location;


    $response = $client->index($params);

    $i++;
}
