<?php

namespace FrankFlow\LaravelForgeLogs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \FrankFlow\LaravelForgeLogs\LaravelForgeLogs
 */
class LaravelForgeLogs extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \FrankFlow\LaravelForgeLogs\LaravelForgeLogs::class;
    }
}
