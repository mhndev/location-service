<?php
namespace mhndev\locationService\exception;

/**
 * Class handler
 * @package mhndev\locationService\exception
 */
class handler
{

    /**
     * @param \Exception $e
     * @param $response
     * @param $container
     * @return mixed
     * @throws \Exception
     */
    public function render(\Exception $e , $response ,$container)
    {

        $container->logger->addError($e);
        throw  $e ;


        if ($e instanceof  \Exception) {
            return ((new JsonApiPresenter())
                ->setStatusCode(500)
                ->setStatus(ResponseStatuses::ERROR)
                ->setMessage('exception')
                ->setApiErrorCode(12)
                ->toJsonResponse($response));
        }


        else{
            $container->logger->addError($e);
            throw  $e ;

        }
    }
}
