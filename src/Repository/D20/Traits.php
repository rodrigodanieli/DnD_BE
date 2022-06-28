<?php

namespace App\Repository\D20;

use App\Repository\Repository;

class Traits extends Repository
{
    protected $table = "traits";
    protected $choices = true;
    protected $relational_map = true;
}