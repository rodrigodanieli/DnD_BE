<?php

namespace App\System;

use PDO;
use Sohris\Core\Logger;
use Sohris\Core\Utils;
use Throwable;

class Database extends PDO
{
    private static $logger;

    public function __construct()
    {
        $configs = Utils::getConfigFiles('database');
        if (!self::$logger)
            self::$logger = new Logger('Database');
        parent::__construct("mysql:host=$configs[host];dbname=$configs[base]", $configs['user'], $configs['pass']);
    }

    public function runQuery(string $query, array $param = [])
    {
        try {
            $stmt = $this->prepare($query);

            foreach ($param as $key => $value) {
                if ($value == "NULL" || trim($value) == "") {
                    $stmt->bindValue($key + 1, $value, \PDO::PARAM_NULL);
                } else if (
                    $value == "False" ||
                    $value == "false" ||
                    $value == "True" ||
                    $value == "true" ||
                    $value === true ||
                    $value === false
                ) {
                    $stmt->bindValue($key + 1, $value, \PDO::PARAM_BOOL);
                } else {
                    $stmt->bindValue($key + 1, $value);
                }
            }
            $result = $stmt->execute();
            if ($result)
                return $this->lastInsertId();
            return false;
        } catch (\PDOException $e) {
            self::$logger->critical($e->getMessage(), array_slice($e->getTrace(), 1, 5));
            echo "Error Database" . PHP_EOL;
        } catch (Throwable $e) {
            self::$logger->critical($e->getMessage(), array_slice($e->getTrace(), 1, 5));
            echo "Error Database" . PHP_EOL;
        }
    }
}
