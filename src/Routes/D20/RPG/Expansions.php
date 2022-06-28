<?php

namespace App\Routes\D20\RPG;

use App\Repository\D20\Expansions as D20Expansions;
use Psr\Http\Message\ServerRequestInterface;
use Sohris\Http\Annotations\HttpMethod;
use Sohris\Http\Annotations\Route;
use Sohris\Http\Annotations\Needed;
use Sohris\Http\Annotations\SessionJWT;
use Sohris\Http\Response;
use Sohris\Http\Router\RouterControllers\DRMRouter;

class Expansions extends DRMRouter
{

    /**
     * @Route("/rpg_expansions/")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     */
    public static function list_rpgs(ServerRequestInterface $request)
    {
        $rpgs = new D20Expansions;
        return $rpgs->getAll()->then(function ($all_rpgs) {
            return Response::Json( $all_rpgs);
        });
    }

    /**
     * @Route("/rpg_expansions/get")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "id"
     * })
     */
    public static function get_rpg(ServerRequestInterface $request)
    {
        $rpg = new D20Expansions;
        return $rpg->get($request->REQUEST['id'])->then(function ($rpg) {
            return Response::Json($rpg);
        });
    }

    /**
     * @Route("/rpg_expansions/update")
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
        $rpg = new D20Expansions;
        return $rpg->update($request->REQUEST['id'], $request->REQUEST['field'], $request->REQUEST['value'])->then(function ($rpg) {
            if ($rpg)
                return Response::Json('ok');
            return Response::Json('error');
        });
    }

    /**
     * @Route("/rpg_expansions/new")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "edition_id",
     *  "name",
     *  "release_date"
     * })
     */
    public static function new_rpg(ServerRequestInterface $request)
    {
        $rpg = new D20Expansions;
        return $rpg->new($request->REQUEST)->then(function ($rpg_id) {
            if (!$rpg_id)
                return Response::Json("ERROR", 501);
            return Response::Json(["id" => $rpg_id]);
        });
    }

    
    /**
     * @Route("/rpg_expansions/delete")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "id"
     * })
     */
    public static function delete_rpg(ServerRequestInterface $request)
    {
        $rpg = new D20Expansions;
        return $rpg->delete($request->REQUEST['id'])->then(function ($rpg_id) {
            if (!$rpg_id)
                return Response::Json("ERROR", 501);
            return Response::Json("ok");
        });
    }
}
