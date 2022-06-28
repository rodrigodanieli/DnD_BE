<?php

namespace App\Routes\Auth;

use App\Repository\System\Auth;
use Psr\Http\Message\ServerRequestInterface;
use Sohris\Http\Annotations\HttpMethod;
use Sohris\Http\Annotations\Needed;
use Sohris\Http\Annotations\Route;
use Sohris\Http\Exceptions\StatusHTTPException;
use Sohris\Http\Response;
use Sohris\Http\Router\RouterControllers\DRMRouter;
use Throwable;

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
                throw new StatusHTTPException("INVALID_LOGIN", 406);
            return Response::Json($auth);
        });
    }
}
