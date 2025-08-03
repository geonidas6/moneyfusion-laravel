<?php

namespace Sefako\Moneyfusion\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sefako\Moneyfusion\Moneyfusion
 */
class Moneyfusion extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Sefako\Moneyfusion\Moneyfusion::class;
    }
}
