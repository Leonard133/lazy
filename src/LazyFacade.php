<?php

namespace Leonard133\Lazy;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Leonard133\Lazy\Skeleton\SkeletonClass
 */
class LazyFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lazy';
    }
}
