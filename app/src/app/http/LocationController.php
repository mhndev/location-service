<?php

namespace mhndev\locationService\http;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\RequestOptions;
use mhndev\location\GoogleEstimate;
use mhndev\location\GoogleGeocoder;
use mhndev\location\GuzzleHttpAgent;
use mhndev\locationService\exceptions\InvalidPointException;
use mhndev\locationService\exceptions\ServerConnectOutsideException;
use mhndev\locationService\services\ConvertFinglishToFarsi;
use mhndev\locationService\services\iLocationRepository;
use mhndev\locationService\services\PointLocation;
use mhndev\restHal\HalApiPresenter;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LocationController
 * @package mhndev\locationService\http
 */
class LocationController
{


    /**
     * @var
     */
    protected $repository;


    /**
     * @var PointLocation
     */
    protected $polygonService;


    //todo refactor code for address components to work well with all providers

    /**
     * LocationController constructor.
     * @param iLocationRepository $repository
     * @param $polygonService
     */
    public function __construct(iLocationRepository $repository, $polygonService)
    {
        $this->repository = $repository;

        $this->polygonService = $polygonService;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function geocodeGoogle(Request $request, Response $response)
    {
        $googleClient = (new GoogleGeocoder())->setHttpAgent(
            new GuzzleHttpAgent(
                [
                    RequestOptions::CONNECT_TIMEOUT => 5,
                    RequestOptions::TIMEOUT => 5
                ]
            )
        );

        $result = $googleClient
            ->setLocale('fa-IR')
            ->geocode($request->getQueryParam('q'), 1);


        $data = ['latitude'=>$result[0]['latitude'], 'longitude'=>$result[0]['longitude'] ];

        $response = (new HalApiPresenter('resource'))
            ->setStatusCode(200)
            ->setData($data)
            ->makeResponse($request, $response);

        return $response;

    }


    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function geocodeMapQuest(Request $request, Response $response, $args)
    {
        $json = file_get_contents(env('mapquest_geocode_endpoint').'?key='.env('mapquest_key').'&location='.$request->getQueryParam('q'));
        $jsonArr = json_decode($json);


        $lat = $jsonArr->results[0]->locations[0]->latLng->lat;
        $lon = $jsonArr->results[0]->locations[0]->latLng->lng;

        $data = ['latitude'=>$lat, 'longitude'=>$lon ];

        $response = (new HalApiPresenter('resource'))
            ->setStatusCode(200)
            ->setData($data)
            ->makeResponse($request, $response);

        return $response;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function reverseMapQuest(Request $request, Response $response, $args)
    {
        $json = file_get_contents(env('mapquest_reverse_endpoint').'?key='.env('mapquest_key').'&location='.$request->getQueryParam('lat').','.$request->getQueryParam('lon').'&includeRoadMetadata=true&includeNearestIntersection=true');
        $jsonArr = json_decode($json, true);

        $lat = $jsonArr['results'][0]['locations'][0]['latLng']['lat'];
        $lon = $jsonArr['results'][0]['locations'][0]['latLng']['lng'];

        $name = $jsonArr['results'][0]['locations'][0]['street'];


        $response = (new HalApiPresenter('resource'))
            ->setStatusCode(200)
            ->setData(['name' => $name, 'location' => ['lat' => $lat, 'lon' => $lon]])
            ->makeResponse($request, $response);

        return $response;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function geocode(Request $request, Response $response)
    {
        $elasticResponse = $this->repository->locationSearch(
            $request->getQueryParam('q'),
            1,
            0,
            'digipeyk'
        );

        $response = (new HalApiPresenter('resource'))
            ->setStatusCode(200)
            ->setData($elasticResponse['data'])
            ->makeResponse($request, $response);


        return $response;
    }



    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function reverseGoogle(Request $request, Response $response)
    {
        $googleClient = new GoogleGeocoder();
        $googleClient->setHttpAgent(
            new GuzzleHttpAgent(
                [
                    RequestOptions::CONNECT_TIMEOUT => 5,
                    RequestOptions::TIMEOUT => 5,
                ]
            )
        );

        $result = $googleClient
            ->setLocale('fa-IR')
            ->reverse($request->getQueryParam('lat'), $request->getQueryParam('lon'), 2);


        if(!empty($result[0]['subLocality'])){
            $result['Area'] = $result[0]['subLocality'];
        }else{
            $result['Area'] = $result[0]['locality'];
        }

        
        if(mb_detect_encoding($result[0]['toString']) == 'ASCII' ){

            $converter = new ConvertFinglishToFarsi();

            $newResult = [
                'location'=>[
                    'lat' => $result[0]['latitude'],
                    'lon' => $result[0]['longitude'],
                ],
                'Area' => $result['Area'],

                'slug' => $result[0]['toString'],
                'preview' => $converter->Convert($result[0]['toString'])
            ];

        }else{
            $newResult = [
                'location'=>[
                    'lat' => $result[0]['latitude'],
                    'lon' => $result[0]['longitude'],
                ],
                'Area' => $result['Area'],
                'preview' => $result[0]['toString'],
            ];
        }




        $response = (new HalApiPresenter('resource'))
            ->setStatusCode(200)
            ->setData($newResult)
            ->makeResponse($request, $response);

        return $response;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function estimate(Request $request, Response $response)
    {
        $estimate_client = new GoogleEstimate();

        $result = $estimate_client
            ->setHttpAgent(new GuzzleHttpAgent([
                RequestOptions::CONNECT_TIMEOUT => 5,
                RequestOptions::TIMEOUT => 5,
            ]))
            ->estimate(
                $request->getQueryParam('from'),
                $request->getQueryParam('to'),
                'optimistic'
            );


        $response = (new HalApiPresenter('resource'))
            ->setStatusCode(200)
            ->setData($result)
            ->makeResponse($request, $response);

        return $response;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return array|Response
     */
    public function suggest(Request $request, Response $response)
    {
        //todo cancel suggest for query param less than 2 characters

        $q = $request->getQueryParam('q');

        if( strlen($q) < 3) {
            $data = [];
        }

        else{
            $perPage = $request->getQueryParam('perPage') ? $request->getQueryParam('perPage') : 10;
            $page = $request->getQueryParam('page') ? $request->getQueryParam('page') : 1;


            $from = $perPage * ($page - 1);

            $elasticResponse = $this->repository->locationSearch($q, $perPage, $from);

            $data = [
                'data'  => $elasticResponse['data'],
                'total' => $elasticResponse['total'],
                'count' => $perPage,
                'name'  => 'locations',
                'page'  => $page
            ];
        }




        $response = (new HalApiPresenter('collection'))
            ->setStatusCode(200)
            ->setData($data)
            ->makeResponse($request, $response);

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws InvalidPointException
     * @throws ServerConnectOutsideException
     */
    function reverse(Request $request, Response $response)
    {
        $lat = $request->getQueryParam('lat');
        $lon = $request->getQueryParam('lon');


        if(! $this->polygonService->isInTehran($lat, $lon) ){
            throw new InvalidPointException('points should be in tehran');
        }

        //http://codrspace.com/dpakrk/elasticsearch-using-php-curl-/
        $perPage = $request->getQueryParam('perPage') ? $request->getQueryParam('perPage') : 10;
        $page = $request->getQueryParam('page') ? $request->getQueryParam('page') : 1;

        $from = $perPage * ($page - 1);

        $elasticResponse = $this->repository->geoSearch($lat, $lon, 0.2, 1, $from);

        $total = $elasticResponse['hits']['total'];
        $data = $elasticResponse['hits']['hits'];

        $res = !empty($data[0]['_source']) ? $data[0]['_source'] : [];

        if(empty($res)){
            try{
                return $this->reverseGoogle($request, $response);
            }
            catch(ConnectException $e){
                throw new ServerConnectOutsideException('location server is trying connect google server but no success.');
            }

        }


        $data = [
            'data' => $res,
            'total' => $total,
            'count' => $perPage,
            'name'  => 'locations'
        ];


        $response = (new HalApiPresenter('resource'))
            ->setStatusCode(200)
            ->setData($data['data'])
            ->makeResponse($request, $response);

        return $response;
    }



}
