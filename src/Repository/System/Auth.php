<?php

namespace App\Repository\System;

use App\System\DatabaseAsync;
use Firebase\JWT\JWT;
use React\Promise\Promise;
use Sohris\Core\Utils;

class Auth extends DatabaseAsync
{
    public function doLoginAdmin(string $user_name, string $password): Promise
    {
        $query = "SELECT * FROM dmaster.users WHERE user_name = ? AND password = SHA1(?) AND user_type = 'admin'";
        return $this->runQuery($query, [$user_name, $password])->then(function ($resolve) {
            if (!is_array($resolve) || count($resolve) <= 0)
                return false;

            $sec_key = Utils::getConfigFiles('http')['jwt_key'];
            $hostname = Utils::getConfigFiles('http')['hostname'];
            $date = new \DateTime("now");
            $now = $date->getTimestamp();
            $date->add(new \DateInterval('P5D'));
            $exp = $date->getTimestamp();

            $payload = array(
                "iss" => $hostname,
                "aud" => $hostname,
                "iat" => $now,
                "exp" => $exp,
                "data" => Utils::jsonEncodeUTF8($resolve[0])
            );
            return JWT::encode($payload, $sec_key, 'HS256');
        }, fn () => false);
    }
}
