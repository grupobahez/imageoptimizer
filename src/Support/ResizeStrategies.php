<?php

namespace Grupobahez\Imageoptimizer\Support;

class ResizeStrategies
{
    public const FIT = 'fit';

    public const FILL = 'fill';

    public const COVER = 'cover';

    public const CROP = 'crop';

    public const CROP_TOP_LEFT = 'crop-top-left';

    public const CROP_TOP_RIGHT = 'crop-top-right';

    public const CROP_BOTTOM_LEFT = 'crop-bottom-left';

    public const CROP_BOTTOM_RIGHT = 'crop-bottom-right';

    public const CROP_TOP = 'crop-top';

    public const CROP_RIGHT = 'crop-right';

    public const CROP_BOTTOM = 'crop-bottom';

    public const CROP_LEFT = 'crop-left';

    public const LIST = [
        self::FIT,
        self::FILL,
        self::COVER,
        self::CROP,
        self::CROP_TOP_LEFT,
        self::CROP_TOP_RIGHT,
        self::CROP_BOTTOM_LEFT,
        self::CROP_BOTTOM_RIGHT,
        self::CROP_TOP,
        self::CROP_RIGHT,
        self::CROP_BOTTOM,
        self::CROP_LEFT,
    ];
}
