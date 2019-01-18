<?php

namespace Lonban\Vcc\Facades;

use Illuminate\Support\Facades\Facade;

class VccFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Vcc';
    }
}