<?php
namespace mhndev\locationService\http;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use mhndev\locationService\services\ElasticSearch;
use mhndev\media\UploadFile;
use PHPExcel_IOFactory;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class UploadDataAction
 * @package mhndev\locationService\http
 */
class UploadDataAction
{

    function __construct()
    {

    }


    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        ini_set( 'upload_max_size' , '64M' );
        ini_set( 'post_max_size', '64M');
        ini_set( 'max_execution_time', '300' );

        $uploadedFile = UploadFile::store('data', 'location_data');

        $uploadedFileName = basename($uploadedFile['path']);


        $inputFileType = 'Excel2007';
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($uploadedFile['path']);

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        $count = count($sheetData);
        $result = [];

        for ($i = 2; $i < $count; $i++){
            if(
                !empty($sheetData[$i]['H']) &&
                !empty($sheetData[$i]['I']) &&
                !empty($sheetData[$i]['J']) &&
                !empty($sheetData[$i]['K']) &&
                !empty($sheetData[$i]['C'])
            ){

                $result[] = [
                    'id' => $i - 1,
                    'type' => 'intersection',
                    'preview' => rtrim(ltrim($sheetData[$i]['I'] )),
                    'location' => [
                        'lat' => (float) explode(',', $sheetData[$i]['H'])[0],
                        'lon' => (float) explode(',', $sheetData[$i]['H'])[1]
                    ],
                    'district' => $sheetData[$i]['B'],
                    'Area' => $sheetData[$i]['C'],
                    'first' => [
                        'type' => $sheetData[$i]['D'],
                        'names' => [
                            'name' => rtrim(ltrim($sheetData[$i]['E'] )),
                            'slug' => 'empty'
                        ]
                    ],
                    'second' => [
                        'type' => $sheetData[$i]['F'],
                        'names' => [
                            'name' => rtrim(ltrim($sheetData[$i]['G'] )),
                            'slug' => 'empty'
                        ]
                    ],
                    'search' => implode(',', [ $sheetData[$i]['J'], $sheetData[$i]['K']  ]),
                ];


            }

        }

        $location_json_path =
            ROOT_DOCKER.
            DIRECTORY_SEPARATOR.
            '.docker-compose'.
            DIRECTORY_SEPARATOR.
            'web-server'.
            DIRECTORY_SEPARATOR.
            'feed'.
            DIRECTORY_SEPARATOR.
            'locations';


/*        foreach (glob($location_json_path.DIRECTORY_SEPARATOR."*.json") as $filename){
            unlink($filename);
        }*/

        $filename = $location_json_path.DIRECTORY_SEPARATOR.explode('.',$uploadedFileName)[0].'.json';

        $text = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
        $file = fopen( $filename, 'w');
        fwrite($file, $text);

        $this->indexToElastic();
    }


    protected function indexToElastic()
    {
        $index = 'digipeyk';
        $type = 'places';
        $hosts = [ 'host' => env('ELASTIC_DB_HOST'), 'port' => env('ELASTIC_DB_PORT')];
        $client = ClientBuilder::create()
            ->setHosts($hosts)
            ->build();

        $repository = new ElasticSearch($client, $index, $type);


        try{
            exec('curl -XDELETE '.env('ELASTIC_DB_HOST').':'.env('ELASTIC_DB_PORT').'/'.$index.'/'.$type);
            exec('curl -XDELETE '.env('ELASTIC_DB_HOST').':'.env('ELASTIC_DB_PORT').'/'. $index);
            //$repository->deleteIndex('digipeyk');
        }catch (Missing404Exception $exception){
            // do nothing
        }

        try {

            exec('
                curl -XPUT ' . env('ELASTIC_DB_HOST') . ':' . env('ELASTIC_DB_PORT') . '/' . $index . ' -d \'{
                    "settings" : {
                        "number_of_shards" : 1
                    },
                    "mappings" : {
                        "' . $type . '" : {
                            "properties" : {
                                "location" : { "type" : "geo_point"}
                            }
                                
                        }
                    }
                }\'
           ');


        }catch (\Exception $e){
            die($e->getMessage());
        }

        $dir = '/docker/feed/locations/';
        $locations = [];

        foreach (glob($dir."*.json") as $filename){

            $data = json_decode(file_get_contents($filename), true);

            $locations = array_merge($data, $locations);
        }


        $i = 1;

        foreach($locations as $location){

            echo $i."\n";

            if(!empty($location['names']))
                unset($location['names']);

            $params['index'] = $index;
            $params['id']    = $i;
            $params['type']  = $type;
            $params['body']  = $location;

            $client->index($params);

            $i++;
        }

    }
}
