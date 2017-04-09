<?php
namespace mhndev\locationService\http;

use mhndev\locationService\services\PointLocation;
use mhndev\restHal\HalApiPresenter;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PointInPolygon
 * @package mhndev\locationService\http
 */
class PointInPolygonAction
{

    /**
     * @var PointLocation
     */
    protected $polygonService;


    /**
     * @var array
     */
    protected $coordinates;


    /**
     * PointInPolygon constructor.
     * @param $polygonService
     */
    public function __construct($polygonService)
    {
        $this->polygonService = $polygonService;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getQueryParams();

        $response = (new HalApiPresenter('resource'))
            ->setStatusCode(200)
            ->setData([ 'is_in' => $this->polygonService->isInTehran($data['lat'], $data['lon']) ])
            ->makeResponse($request, $response);

        return $response;
    }

}
