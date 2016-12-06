<?php
namespace mhndev\locationService\middlewares;



/**
 * Class MiddlewareAuthorization
 * @package mhndev\orderService\auth
 */
class MiddlewareCors
{


    /**
     * MiddlewareCors constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Authorization middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param  callable $next Next middleware
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $route = $request->getAttribute("route");

        $methods = [];

        if (!empty($route)) {
            $pattern = $route->getPattern();

            foreach ($this->container->router->getRoutes() as $route) {
                if ($pattern === $route->getPattern()) {
                    $methods = array_merge_recursive($methods, $route->getMethods());
                }
            }
            //Methods holds all of the HTTP Verbs that a particular route handles.
        } else {
            $methods[] = $request->getMethod();
        }

        $response = $next($request, $response);


        return $response->withHeader("Access-Control-Allow-Methods", implode(",", $methods));
    }


}
