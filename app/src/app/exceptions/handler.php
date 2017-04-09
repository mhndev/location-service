<?php
namespace mhndev\locationService\exceptions;

use mhndev\restHal\HalApiPresenter;
use Monolog\Logger;
use Slim\Collection;

/**
 * Class handler
 * @package mhndev\locationService\exceptions
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

        /** @var Collection $settings */
        $settings = $container->settings;

        $mode = $settings->get('mode');
        $logger = $container->logger;

        /** @var Logger $logger */
        $logger->addError($e);


        if($mode == 'production'){
            if ($e instanceof AccessDeniedException) {
                return ((new HalApiPresenter('error'))
                    ->setStatusCode(403)
                    ->setData(['message' => 'no access', 'code' => 12])
                    ->makeResponse($request, $response));
            }

            if($e instanceof ServerConnectOutsideException){
                return ((new HalApiPresenter('error'))
                    ->setStatusCode(500)
                    ->setData(['message' => 'server connection problem, try later', 'code' => 13])
                    ->makeResponse($request, $response));
            }

            if($e instanceof InvalidPointException){
                return ((new HalApiPresenter('error'))
                    ->setStatusCode(400)
                    ->setData(['message' => $e->getMessage(), 'code' => 14])
                    ->makeResponse($request, $response));
            }


        }

        elseif ($mode == 'develop'){
            throw $e;
        }


        elseif ($mode == 'debug'){
            $error = [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'file'=>$e->getFile(),
            ];

            return ((new HalApiPresenter('error'))
                ->setStatusCode(500)
                ->setData($error)
                ->makeResponse($request, $response));
        }

        else{
            throw new InvalidApplicationRunMode(
                sprintf('Application Run Mode can be one of %s, %s given.',
                    implode(['debug', 'develop', 'production']),$mode)
            );

        }


    }
}
