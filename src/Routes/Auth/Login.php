<?php

namespace App\Routes\Auth;

use App\Repository\System\Auth;
use Exception;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface;
use Sohris\Core\Utils;
use Sohris\Http\Annotations\HttpMethod;
use Sohris\Http\Annotations\Needed;
use Sohris\Http\Annotations\Route;
use Sohris\Http\Annotations\SessionJWT;
use Sohris\Http\Exceptions\StatusHTTPException;
use Sohris\Http\Response;
use React\Promise\Promise;
use Sohris\Http\Router\RouterControllers\DRMRouter;

class Login extends DRMRouter
{

    /**
     * @Route("/auth/login_admin")
     * @HttpMethod("POST")
     * @Needed({
     *      "username",
     *      "password"
     * })
     */
    public static function login(ServerRequestInterface $request)
    {
        $auth = new Auth;
        return $auth->doLoginAdmin($request->REQUEST['username'], $request->REQUEST['password'])->then(function($auth)
        {
            if(!$auth)
                return Response::Json("INVALID LOGIN");
            return Response::Json($auth);
        }, fn()=>Response::Json("INTERNAL ERROR", 500));
    }
}
