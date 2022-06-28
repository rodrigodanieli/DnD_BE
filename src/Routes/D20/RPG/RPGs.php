<?php

namespace App\Routes\D20\RPG;

use App\Repository\D20\RPGs as D20RPGs;
use App\System\Utils;
use Psr\Http\Message\ServerRequestInterface;
use Sohris\Http\Annotations\HttpMethod;
use Sohris\Http\Annotations\Route;
use Sohris\Http\Annotations\Needed;
use Sohris\Http\Annotations\SessionJWT;
use Sohris\Http\Response;
use Sohris\Http\Router\RouterControllers\DRMRouter;

class RPGs extends DRMRouter
{

    /**
     * @Route("/rpgs/")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     */
    public static function list_rpgs(ServerRequestInterface $request)
    {
        $rpgs = new D20RPGs;
        return $rpgs->getAll()->then(function ($all_rpgs) {
            return Response::Json( $all_rpgs);
        });
    }

    /**
     * @Route("/rpgs/get")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "id"
     * })
     */
    public static function get_rpg(ServerRequestInterface $request)
    {
        $rpg = new D20RPGs;
        return $rpg->get($request->REQUEST['id'])->then(function ($rpg) {
            return Response::Json($rpg);
        });
    }

    /**
     * @Route("/rpgs/update")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "id",
     *  "field",
     *  "value"
     * })
     */
    public static function update_rpg(ServerRequestInterface $request)
    {
        $rpg = new D20RPGs;
        return $rpg->update($request->REQUEST['id'], $request->REQUEST['field'], $request->REQUEST['value'])->then(function ($rpg) {
            if ($rpg)
                return Response::Json('ok');
            return Response::Json('error');
        });
    }

    /**
     * @Route("/rpgs/new")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "name",
     *  "abbr",
     *  "release_date"
     * })
     */
    public static function new_rpg(ServerRequestInterface $request)
    {
        $rpg = new D20RPGs;
        return $rpg->new($request->REQUEST)->then(function ($rpg_id) {
            if (!$rpg_id)
                return Response::Json("ERROR", 501);
            return Response::Json(["id" => $rpg_id]);
        });
    }

    
    /**
     * @Route("/rpgs/delete")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "id"
     * })
     */
    public static function delete_rpg(ServerRequestInterface $request)
    {
        $rpg = new D20RPGs;
        return $rpg->delete($request->REQUEST['id'])->then(function ($rpg_id) {
            if (!$rpg_id)
                return Response::Json("ERROR", 501);
            return Response::Json("ok");
        });
    }
}
