<?php

namespace mhndev\locationService\middlewares;

use mhndev\orderService\exceptions\AccessDeniedException;
use Poirot\OAuth2\Resource\Validation\AuthorizeByRemoteServer;
use Poirot\OAuth2\Server\Exception\exOAuthServer;

/**
 * Class MiddlewareAuthorization
 * @package mhndev\orderService\auth
 */
class MiddlewareAuthorization
{

    /**
     * @var string
     */
    private static $ownerIdentifier;

    /**
     * @var array
     */
    private static $scopes;

    /**
     * @var mixed
     */
    private static $user;

    /**
     * @var string
     */
    private static $token;


    /**
     * MiddlewareCors constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }




    /**
     * @return mixed
     */
    public static function user()
    {
        return self::$user;
    }

    /**
     * @return string
     */
    public static function token()
    {
        return self::$token;
    }

    /**
     * @return string
     */
    public static function scopes()
    {
        return self::$scopes;
    }


    /**
     * @return string
     */
    public static function ownerIdentifier()
    {
        return self::$ownerIdentifier;
    }



    /**
     * Authorization middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param  callable $next Next middleware
     * @return \Psr\Http\Message\ResponseInterface
     * @throws AccessDeniedException
     */
    public function __invoke($request, $response, $next)
    {
        $oauthServerAddress = env('oauth_server');
        $serverAuthorizationHeader = 'Basic '.base64_encode(env('oauth_client_id').':'.env('oauth_client_secret'));
        $authorizationServer = new AuthorizeByRemoteServer($oauthServerAddress, $serverAuthorizationHeader);


        try{
            $accessToken = $authorizationServer->hasValidated($request);

            self::$token = $authorizationServer->assertAccessToken($request);
            self::$scopes = $accessToken->getScopes();

            self::$ownerIdentifier = $accessToken->getOwnerIdentifier();



            //todo check for access to resources for now pass all request
            if(0){
                throw new AccessDeniedException;
            }else{

            }
        }catch (exOAuthServer $e){
            throw new AccessDeniedException;
        }


        $response = $next($request, $response);

        return $response;

    }

}
