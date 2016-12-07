<?php
namespace mhndev\locationService\exceptions;

use mhndev\restHal\HalApiPresenter;
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
        if ($e instanceof AccessDeniedException) {
            return ((new HalApiPresenter('error'))
                ->setStatusCode(403)
                ->setData(['message' => 'no access', 'code' => 12])
                ->makeResponse($request, $response));
        } else {
            $error = json_encode([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'file'=>$e->getFile(),
            ]);

            /** @var \Monolog\Logger $logger */
            $logger = $container->logger;

            $logger->addError($error);

            /** @var Collection $settings */
            $settings = $container->settings;

            if($settings->get('mode') == 'debug'){
                $error = ['message' => $e->getMessage(), 'code' => $e->getCode()];
            }
            else{
                $error = ['message'=>'error'];
                ///dev/stderr
                ///dev/stdout
            }

            return ((new HalApiPresenter('error'))
                ->setStatusCode(500)
                ->setData($error)
                ->makeResponse($request, $response));

        }


    }
}
