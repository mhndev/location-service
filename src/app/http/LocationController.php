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
    public function geocodeGoogle(Request $request, Response $response)
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
    public function geocode(Request $request, Response $response)
    {
        $elasticResponse = ElasticSearch::locationSearch(
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

        $googleClient->setHttpAgent(new GuzzleHttpAgent());

        $result = $googleClient
            ->setLocale('fa-IR')
            ->reverse($request->getQueryParam('lat'), $request->getQueryParam('lon'), 3);

        $newResult = [
            'location'=>[
                'lat' => $result[0]['latitude'],
                'lon' => $result[0]['longitude'],
            ],

            'name' => $result[0]['toString']
        ];


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
            ->setHttpAgent(new GuzzleHttpAgent())
            ->estimate($request->getQueryParam('from'), $request->getQueryParam('to'), 'optimistic');

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
    function reverse(Request $request, Response $response)
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


        $res = !empty($data[0]['_source']) ? $data[0]['_source'] : [];

        if(empty($res)){
            return $this->reverseGoogle($request, $response);
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
