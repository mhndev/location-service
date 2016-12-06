<?php
namespace mhndev\locationService\services;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;


/**
 * Class ElasticSearch
 * @package mhndev\locationService
 */
class ElasticSearch implements iLocationRepository
{
    /**
     * @var
     */
    protected  $resultFields;


    /**
     * @var Client
     */
    protected  $elasticClient;


    /**
     * ElasticSearch constructor.
     * @param Client $client
     */
    public function __construct(Client $client = null)
    {
        if($client == null){
            $client = ClientBuilder::create();
        }

        $this->elasticClient = $client;
    }

    public  function setClient($client)
    {
        $this->elasticClient = $client;
    }



    /**
     * @param $lat
     * @param $long
     * @param $distance
     * @param $size
     * @param $from
     * @param array $fields
     * @param $index
     * @return array
     */
    public function geoSearch($index, $lat, $long, $distance = 100 , $size = 10, $from = 0, $fields = [])
    {
        $fields = $fields == [] ? ['id', 'name', 'slug', 'location'] : $fields;

        $params = [
            'index' => $index,
            'type' => 'place',
            'body' => [
                'size' => (int)$size,
                'from' => (int)$from,
                '_source' => $fields,

                'query' => [
                    "filtered" => [
                        'query' =>[
                            'match_all' => []
                        ],
                        'filter' => [
                            "geo_distance" => [
                                'distance' => $distance,
                                'location' => [
                                    'lat' => (float)$lat,
                                    'lon' => (float)$long
                                ]
                            ]
                        ]
                    ]
                ],
//                'sort' => [
//                    '_geo_distance' => [
//                        'location' => [
//                            'lat' => (float)$lat,
//                            'lon' => (float)$long
//                        ],
//                        'order' => 'asc',
//                        'unit' => 'm',
//                        'distance_type' => 'plane'
//                    ]
//                ]

            ]
        ];


        return $this->elasticClient->search($params);

        //return self::jsonTransformGeoSearch($es->search($params)['hits']['hits']);

    }



    /**
     * @param $query
     * @param $size
     * @param $from
     * @param $index
     * @param array $fields
     * @return array
     */
    public function locationSearch($index, $query, $size = 10, $from = 0, $fields = [])
    {
        $fields = $fields == [] ? ['id', 'name', 'slug', 'location', 'search'] : $fields;

        $encoding = mb_detect_encoding($query);


        if($encoding == 'ASCII'){
            $query = strtolower($query);
        }


        $params = [
            'index' => $index,
            'type' => 'place',
            'body' => [
                '_source' => $fields,

                'sort' => ['_score' => ['order' => 'desc']],


//                'query' => [
//                    'wildcard' => [
//                        'search' => '*'.$query.'*'
//                    ]
//                ],

                'query' => [
                    'match_phrase_prefix' => [
                        'search'=>[
                            'query' => $query,
                            'max_expansions' => 5
                        ]
                    ]
                ],


//                'query' => [
//                    'regexp' => [
//                        'search'=>$query.'.*',
//                    ]
//                ],



//                'query' => [
//                    'match' => [
//                        'search' => [
//                            'query' => '*'.$query.'*',
//                            'minimum_should_match'=> '50%',
////                            'operator' => 'and'
//                        ]
//                    ]
//                ],

//                'query'=>[
//                    'bool' => [
//                        'must' => [
//                            'wildcard' => [
//                                '_all' =>[
//                                    'value' => '*'.$query.'*'
//                                ]
//                            ]
//                        ]
//                    ]
//                ],



                'from' => $from,
                'size' => $size
            ]
        ];

        $searchResult = $this->elasticClient->search($params);

        $result = $this->jsonTransformSearchLocation($searchResult['hits']['hits']);

        return ['data' => $result, 'total' => $searchResult['hits']['total']];
    }


    /**
     * @param $indexName
     * @param $type
     * @param $data
     */
    public function store($indexName, $type, $data)
    {
        $this->elasticClient->delete(['index'=>$indexName,'type'=>$type,'id'=>$data['id']]);

        // $es->indices()->delete($deleteParams);


        $this->elasticClient->index(['index' => $indexName, 'type' => $type, 'body' => $data]);

    }

    /**
     * @param $indexName
     * @param $type
     * @param $id
     * @param $data
     */
    public function update($indexName, $type, $id, $data)
    {
        try {
            $searchParams['index'] = $indexName;
            $searchParams['type'] = $type;
            $searchParams['body']['query']['match']['id'] = $id;
            $result = $this->elasticClient->search($searchParams);

            $elasticId = $result['hits']['hits'][0]['_id'];

            $params = [
                'index' => $indexName,
                'type' => $type,
                'id' => $elasticId,
                'body' => [
                    'doc' => $data
                ]
            ];

            $this->elasticClient->update($params);


        } catch (\Exception $e) {

        }


    }




    /**
     * @param $indexName
     * @param $type
     * @param $id
     */
    public function delete($indexName, $type, $id)
    {
        $searchParams['index'] = $indexName;
        $searchParams['type'] = $type;
        $searchParams['body']['query']['match']['id'] = $id;
        $result = $this->search($searchParams);
        $elasticId = $result['hits']['hits'][0]['_id'];

        $params = [
            'index' => $indexName,
            'type' => $type,
            'id' => $elasticId
        ];


        $this->elasticClient->delete($params);
    }

    /**
     * @param array $fields
     */
    public function setResultFiles($fields)
    {
        $fields === null ? $this->resultFields = null : $this->resultFields = explode(',', $fields);
    }


    /**
     * @param array $data
     * @return array
     */
    protected function jsonTransformGeoSearch(array $data)
    {
        $jsonResponse = [];
        $counter = 0;

        foreach ($data as $item) {
            $jsonResponse[] = array_merge($item['_source'],['distance_per_meters' => $item['sort'][0]]);
            $counter++;
        }

        return $jsonResponse;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function jsonTransformSearchLocation(array $data)
    {
        $jsonResponse = [];
        $counter = 0;

        foreach ($data as $item) {
            $jsonResponse[] = array_merge($item['_source']);
            $counter++;
        }

        return $jsonResponse;
    }



    /**
     * @param $indexName
     */
    public function deleteIndex($indexName)
    {
        $deleteParams = [
            'index' => $indexName
        ];
        $this->elasticClient->indices()->delete($deleteParams);

    }

}
