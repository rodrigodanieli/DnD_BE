<?php

namespace App\Routes\D20\RPG;

use App\Repository\D20\Editions as D20Editions;
use Psr\Http\Message\ServerRequestInterface;
use Sohris\Http\Annotations\HttpMethod;
use Sohris\Http\Annotations\Route;
use Sohris\Http\Annotations\Needed;
use Sohris\Http\Annotations\SessionJWT;
use Sohris\Http\Response;
use Sohris\Http\Router\RouterControllers\DRMRouter;

class Editions extends DRMRouter
{

    /**
     * @Route("/rpg_editions/")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     */
    public static function list_rpgs(ServerRequestInterface $request)
    {
        $rpgs = new D20Editions;
        return $rpgs->getAll()->then(function ($all_rpgs) {
            return Response::Json( $all_rpgs);
        });
    }

    /**
     * @Route("/rpg_editions/get")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "id"
     * })
     */
    public static function get_rpg(ServerRequestInterface $request)
    {
        $rpg = new D20Editions;
        return $rpg->get($request->REQUEST['id'])->then(function ($rpg) {
            return Response::Json($rpg);
        });
    }

    /**
     * @Route("/rpg_editions/update")
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
        $rpg = new D20Editions;
        return $rpg->update($request->REQUEST['id'], $request->REQUEST['field'], $request->REQUEST['value'])->then(function ($rpg) {
            if ($rpg)
                return Response::Json('ok');
            return Response::Json('error');
        });
    }

    /**
     * @Route("/rpg_editions/new")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "rpg_id",
     *  "name",
     *  "version"
     *  "release_date"
     * })
     */
    public static function new_rpg(ServerRequestInterface $request)
    {
        $rpg = new D20Editions;
        return $rpg->new($request->REQUEST)->then(function ($rpg_id) {
            if (!$rpg_id)
                return Response::Json("ERROR", 501);
            return Response::Json(["id" => $rpg_id]);
        });
    }

    
    /**
     * @Route("/rpg_editions/delete")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "id"
     * })
     */
    public static function delete_rpg(ServerRequestInterface $request)
    {
        $rpg = new D20Editions;
        return $rpg->delete($request->REQUEST['id'])->then(function ($rpg_id) {
            if (!$rpg_id)
                return Response::Json("ERROR", 501);
            return Response::Json("ok");
        });
    }
}
