<?php

namespace Grupobahez\Imageoptimizer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static optimize(string $originalImagePath, array $array)
 * @method static optimizeFromUrl(string $originalImageUrl, array $array)
 */
class ImageOptimizer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'imageoptimizer';
    }
}
