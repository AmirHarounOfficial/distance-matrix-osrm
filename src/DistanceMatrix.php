<?php

namespace Haroun\DistanceMatrix;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array route(array $coordinates)
 * @method static array matrix(array $coordinates)
 *
 * @see \Haroun\DistanceMatrix\DistanceMatrixManager
 */
class DistanceMatrix extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'distance-matrix';
    }
}
