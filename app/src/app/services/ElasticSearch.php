<?php
namespace mhndev\locationService\services;

use Elasticsearch\ClientBuilder;


/**
 * Class ElasticSearch
 * @package mhndev\locationService
 */
class ElasticSearch
{
    /**
     * @var
     */
    protected static $resultFields;

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
    public static function geoSearch($lat, $long, $distance, $size, $from,$index, $fields = [])
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
        $es = ClientBuilder::create()->build();



        return $es->search($params);

        //return self::jsonTransformGeoSearch($es->search($params)['hits']['hits']);

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
    public static function geoSearch2($lat, $long, $distance, $size, $from,$index, $fields = [])
    {
        $fields = $fields == [] ? ['id', 'name', 'slug', 'location'] : $fields;

        $params = [
            'index' => $index,
            'type' => 'location',
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
                                ],
                                'unit' => 'm',
                                'distance_type' => 'plane'
                            ]
                        ]
                    ]
                ]

            ]
        ];

        $search_host = 'localhost';
        $search_port = '9200';
        $index = 'digipeyk';  // suppose this is your db name
        $doc_type = 'location';
        $baseUri = "localhost:9200/digipeyk/location/_search?pretty=true";

        $method = "GET";
        $queryData = $params ;
        $url = 'http://'.$search_host.':'.$search_port.'/'.$index.'/'.$doc_type.'/_search?'.http_build_query($queryData) ;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, 9200);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        $result = curl_exec($ch);
        curl_close($ch);
        $ary = json_decode($result,true);


        return $ary;
    }



    /**
     * @param $query
     * @param $size
     * @param $from
     * @param $index
     * @param array $fields
     * @return array
     */
    public static function locationSearch($query, $size, $from, $index, $fields = [])
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


        $client = ClientBuilder::create()->build();

        $searchResult = $client->search($params);

        $result = self::jsonTransformSearchLocation($searchResult['hits']['hits']);

        return ['data' => $result, 'total' => $searchResult['hits']['total']];
    }


    /**
     * @param $indexName
     * @param $type
     * @param $data
     */
    public static function index($indexName, $type, $data)
    {
        $es = ClientBuilder::create()->build();
        //   $es->delete(['index'=>$indexName,'type'=>$type,'id'=>$data['id']]);

        // $es->indices()->delete($deleteParams);


        $es->index(['index' => $indexName, 'type' => $type, 'body' => $data]);

    }

    /**
     * @param $indexName
     * @param $type
     * @param $id
     * @param $data
     */
    public static function update($indexName, $type, $id, $data)
    {
        try {
            $es = ClientBuilder::create()->build();//
//        self::index($indexName,$type,$data);


            $searchParams['index'] = $indexName;
            $searchParams['type'] = $type;
            $searchParams['body']['query']['match']['id'] = $id;
            $result = $es->search($searchParams);

            $elasticId = $result['hits']['hits'][0]['_id'];


            $es = ClientBuilder::create()->build();


            $params = [
                'index' => $indexName,
                'type' => $type,
                'id' => $elasticId,
                'body' => [
                    'doc' => $data
                ]
            ];
            $es->update($params);


        } catch (\Exception $e) {

        }


    }




    /**
     * @param $indexName
     * @param $type
     * @param $id
     */
    public static function delete($indexName, $type, $id)
    {
        $es = ClientBuilder::create()->build();
        $searchParams['index'] = $indexName;
        $searchParams['type'] = $type;
        $searchParams['body']['query']['match']['id'] = $id;
        $result = $es->search($searchParams);
        $elasticId = $result['hits']['hits'][0]['_id'];

        $es = ClientBuilder::create()->build();
        $params = [
            'index' => $indexName,
            'type' => $type,
            'id' => $elasticId
        ];


        $es->delete($params);

    }

    /**
     * @param array $fields
     */
    public static function setResultFiles($fields)
    {
        $fields === null ? self::$resultFields = null : self::$resultFields = explode(',', $fields);
    }

    /**
     * @param $terms
     * @return array
     */
    public static function prepareTerms($terms)
    {
        $mustTerms = [];

        $terms = explode(',', $terms);
        foreach ($terms as $term) {
            $term = explode(":", $term);
            if (strpos($term[1], '^') == true) {
                $multipleTerms = explode("^", $term[1]);

                $mustTerms[] = ['terms' => [$term[0] => [$multipleTerms[0], $multipleTerms[1]]]];

            } else {
                $mustTerms[] = ['term' => [$term[0] => $term[1]]];
            }
        }
        return $mustTerms;
    }

    /**
     * @param array $data
     * @return array
     */
    protected static function jsonTransformGeoSearch(array $data)
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
    protected static function jsonTransformSearchLocation(array $data)
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
    public static function deleteIndex($indexName)
    {
        $es = ClientBuilder::create()->build();
        $deleteParams = [
            'index' => $indexName
        ];
        $es->indices()->delete($deleteParams);

    }
}
