<?php

namespace mhndev\locationService\http;

use mhndev\location\GoogleEstimate;
use mhndev\location\GoogleGeocoder;
use mhndev\location\GuzzleHttpAgent;
use mhndev\locationService\services\ElasticSearch;
use mhndev\restHal\HalApiPresenter;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LocationController
 * @package mhndev\locationService\http
 */
class LocationController
{

    public function __construct()
    {

    }


    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function geocode(Request $request, Response $response)
    {
        $googleClient = (new GoogleGeocoder())->setHttpAgent(new GuzzleHttpAgent());

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
     * @return Response
     */
    public function reverse(Request $request, Response $response)
    {
        $googleClient = new GoogleGeocoder();

        $googleClient->setHttpAgent(new GuzzleHttpAgent());

        $result = $googleClient
            ->setLocale('fa-IR')
            ->reverse($_GET['lat'], $_GET['lon'], 3);


        $response = (new HalApiPresenter('resource'))
            ->setStatusCode(200)
            ->setData($result[2]['toString'])
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
            ->setHttpAgent(new GuzzleHttpAgent())
            ->estimate($_GET['origin'], $_GET['destination'], 'optimistic');


        $price = 5000 + (float)$result['distance'] * 600;


        $result['price'] = $price;


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
        $perPage = $request->getQueryParam('perPage') ? $request->getQueryParam('perPage') : 10;
        $page = $request->getQueryParam('page') ? $request->getQueryParam('page') : 1;

        $from = $perPage * ($page - 1);

        $elasticResponse = ElasticSearch::locationSearch(
            $request->getQueryParam('q'),
            $perPage,
            $from,
            'digipeyk'
        );

        $data = [
            'data' => $elasticResponse['data'],
            'total' => $elasticResponse['total'],
            'count' => $perPage,
            'name'  => 'locations'
        ];


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
     */
    function reverseGeocode(Request $request, Response $response)
    {
        //http://codrspace.com/dpakrk/elasticsearch-using-php-curl-/
        $perPage = $request->getQueryParam('perPage') ? $request->getQueryParam('perPage') : 10;
        $page = $request->getQueryParam('page') ? $request->getQueryParam('page') : 1;

        $from = $perPage * ($page - 1);

        $elasticResponse = ElasticSearch::geoSearch(
            $request->getQueryParam('lat'),
            $request->getQueryParam('lon'),
            100,
            3,
            $from,
            'digipeyk'
        );

        $total = $elasticResponse['hits']['total'];
        $data = $elasticResponse['hits']['hits'];


        $data = [
            'data' => $data,
            'total' => $total,
            'count' => $perPage,
            'name'  => 'locations'
        ];

        $response = (new HalApiPresenter('collection'))
            ->setStatusCode(200)
            ->setData($data)
            ->makeResponse($request, $response);

        return $response;
    }



}
