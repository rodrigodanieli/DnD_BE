<?php

namespace App\System;

use Sohris\Core\Logger;
use Sohris\Mysql\Pool;
use Throwable;

class DatabaseAsync
{
    private static $logger;
    private static $pool;


    public function __construct()
    {
        if (!self::$logger)
            self::$logger = new Logger('Database');
        if (!self::$pool)
            self::$pool = new Pool;
    }

    public function runQuery(string $query, array $param = [])
    {
        try {
            return self::$pool->exec($query, $param)->then(function($resolve){
                return $resolve;
            }, function ($reject) {
                self::$logger->critical($reject);
                return false;
            });
        } catch (\PDOException $e) {
            self::$logger->critical($e->getMessage(), array_slice($e->getTrace(), 1, 5));
            echo "Error Database" . PHP_EOL;
        } catch (Throwable $e) {
            self::$logger->critical($e->getMessage(), array_slice($e->getTrace(), 1, 5));
            echo "Error Database" . PHP_EOL;
        }
    }
}
