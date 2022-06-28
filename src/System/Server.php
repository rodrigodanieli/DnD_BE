<?php

namespace App\System;

use App\Repository\D20\Editions;
use App\Repository\D20\RPGs;
use App\Repository\D20\Traits;
use React\EventLoop\Loop;
use Sohris\Core\Component\AbstractComponent;
use Sohris\Core\Logger;
use Sohris\Mysql\Pool;

class Server extends AbstractComponent
{
    private $module_name = "DnD";

    private $server;

    private $logger;

    public function __construct()
    {
        $this->loop = Loop::get();
        $this->logger = new Logger('DND');
    }

    public function install()
    {
    }

    public function start()
    {        
    }
}