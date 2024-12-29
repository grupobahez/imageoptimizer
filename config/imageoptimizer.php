<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Quality (in percentage)
    |--------------------------------------------------------------------------
    | Numeric value between 0 and 100 that indicates the quality of the resulting image.
    */
    'quality' => 80,

    /*
    |--------------------------------------------------------------------------
    | Output Format
    |--------------------------------------------------------------------------
    | The format to which the resulting image will be converted.
    | Common options: 'jpg', 'png', 'webp', etc.
    */
    'format' => 'webp',

    /*
    |--------------------------------------------------------------------------
    | Resizing Strategy (crop, fill, fit, cover)
    |--------------------------------------------------------------------------
    | - crop: Crops the image to the exact size.
    | - fill: Fills the image to the exact size.
    | - fit: Adjusts the image while maintaining the aspect ratio; the image size may vary.
    | - cover: Similar to fit but ensures the size is completely covered.
    */
    'strategy' => 'fit',

    /*
    |--------------------------------------------------------------------------
    | Thumbnail Sizes (similar to WordPress)
    |--------------------------------------------------------------------------
    | Key => [width, height]
    | The package will generate these sizes if requested.
    */
    'thumbnails' => [
        'small' => [150, 150],
        'medium' => [300, 300],
        'large' => [1024, 768],
    ],

];
