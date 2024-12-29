<?php

namespace Grupobahez\Imageoptimizer\Facades;

use Illuminate\Support\Facades\Facade;

class ImageOptimizer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'imageoptimizer';
    }
}
