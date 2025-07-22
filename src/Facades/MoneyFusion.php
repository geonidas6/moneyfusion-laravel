<?php

namespace Vendor\MoneyFusion\Facades;

use Illuminate\Support\Facades\Facade;

class MoneyFusion extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'moneyfusion';
    }
}