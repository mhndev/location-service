<?php

include 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

use Elasticsearch\ClientBuilder;

//$deleteParams = ['index' => 'digipeyk'];

$index = 'digipeyk';
$type = 'places';
$hosts = [ 'host' => env('ELASTIC_DB_HOST'), 'port' => env('ELASTIC_DB_PORT')];
$client = Elasticsearch\ClientBuilder::create()
    ->setHosts($hosts)
    ->build();

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

$dir = '/docker/data/locations/';
$locations = [];

foreach (glob($dir."*.json") as $filename){

    $data = json_decode(file_get_contents($filename), true);

    $locations = array_merge($data, $locations);
}



foreach($locations as $location){

    echo $i."\n";

    unset($location['latitude']);
    unset($location['longitude']);

    $params['index'] = $index;
    $params['id'] = $location['id'];
    $params['type'] = $type;
    $params['body'] = $location;


    $response = $client->index($params);

    $i++;
}
