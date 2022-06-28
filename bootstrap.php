<?php

use Sohris\Core\Server;
use Sohris\Mysql\Pool;

include "vendor/autoload.php";

$app = new Server;
$app->setRootDir(__DIR__);

$app->loadingServer();

Pool::createConnection();