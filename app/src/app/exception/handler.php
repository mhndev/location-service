<?php
namespace mhndev\locationService\exceptions;

use mhndev\restHal\HalApiPresenter;

/**
 * Class handler
 * @package mhndev\locationService\exception
 */
class handler
{

    /**
     * @param \Exception $e
     * @param $request
     * @param $response
     * @param $container
     * @return mixed
     * @throws \Exception
     */
    public function render(\Exception $e , $request, $response ,$container)
    {


        if ($e instanceof AccessDeniedException) {
            return ((new HalApiPresenter('error'))
                ->setStatusCode(500)
                ->setData(['message' => 'no access', 'code' => 12])
                ->makeResponse($request, $response));
        } else {
            $container->logger->addError($e);
            throw  $e;

        }


    }
}
