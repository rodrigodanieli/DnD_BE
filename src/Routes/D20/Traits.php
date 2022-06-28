<?php

namespace App\Routes\D20;

use App\Repository\D20\Traits as D20Traits;
use App\System\Utils;
use Psr\Http\Message\ServerRequestInterface;
use Sohris\Http\Annotations\HttpMethod;
use Sohris\Http\Annotations\Route;
use Sohris\Http\Annotations\Needed;
use Sohris\Http\Annotations\SessionJWT;
use Sohris\Http\Response;
use Sohris\Http\Router\RouterControllers\DRMRouter;

class Traits extends DRMRouter
{

    /**
     * @Route("/traits/")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     */
    public static function list_traits(ServerRequestInterface $request)
    {
        $trait = new D20Traits;
        return $trait->getAll()->then(function ($all_traits) {
            return Response::Json(array_map(function ($el) {
                $el['description'] = json_decode($el['description'], true);
                $el['proficiencies'] = json_decode($el['proficiencies'], true);
                return $el;
            }, $all_traits));
        });
    }

    /**
     * @Route("/traits/get")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "id"
     * })
     */
    public static function get_trait(ServerRequestInterface $request)
    {
        $trait = new D20Traits;
        return $trait->get($request->REQUEST['id'])->then(function ($trait) {
            $trait['description'] = json_decode($trait['description'], true);
            $trait['proficiencies'] = json_decode($trait['proficiencies'], true);
            return Response::Json($trait);
        });
    }

    /**
     * @Route("/traits/update")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "id",
     *  "field",
     *  "value"
     * })
     */
    public static function update_trait(ServerRequestInterface $request)
    {
        $trait = new D20Traits;
        return $trait->update($request->REQUEST['id'], $request->REQUEST['field'], $request->REQUEST['value'])->then(function ($trait) {
            if ($trait)
                return Response::Json('ok');
            return Response::Json('error');
        });
    }

    /**
     * @Route("/traits/new")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "name",
     *  "description",
     *  "indexed_name",
     *  "proficiencies",
     *  "choices",
     *  "relational_map"
     * })
     */
    public static function new_trait(ServerRequestInterface $request)
    {
        $trait = new D20Traits;
        $request->REQUEST['description'] = Utils::prepareDescriptionToBase($request->REQUEST['description']);
        $request->REQUEST['proficiencies'] = json_encode($request->REQUEST['proficiencies']);
        return $trait->new($request->REQUEST)->then(function ($trait_id) {
            if (!$trait_id)
                return Response::Json("ERROR", 501);
            return Response::Json(["id" => $trait_id]);
        });
    }
    
    /**
     * @Route("/traits/delete")
     * @HttpMethod("POST")
     * @SessionJWT(true)
     * @Needed({
     *  "id"
     * })
     */
    public static function delete_trait(ServerRequestInterface $request)
    {
        $trait = new D20Traits;
        return $trait->delete($request->REQUEST['id'])->then(function ($trait_id) {
            if (!$trait_id)
                return Response::Json("ERROR", 501);
            return Response::Json("ok");
        });
    }
}
