<?php
namespace mhndev\locationService\http;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

/**
 * Class LocationInputAction
 * @package mhndev\locationService\http
 */
class LocationInputAction
{

    protected $renderer;

    /**
     * LocationInputAction constructor.
     * @param PhpRenderer $renderer
     */
    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        return $this->renderer->render($response, "/upload.phtml");
    }
}
