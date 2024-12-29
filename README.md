
# grupobahez/imageoptimizer

Package for optimizing and generating multiple image sizes in Laravel using [Intervention Image](http://image.intervention.io/).

## Installation

```bash
composer require grupobahez/imageoptimizer
```

## Usage

1. Publish the configuration file (optional):
   ```bash
   php artisan vendor:publish --provider="Grupobahez\Imageoptimizer\ImageOptimizerServiceProvider"
   ```
2. Configure the values in `config/imageoptimizer.php`.
3. In your code, use the `ImageOptimizer` facade to optimize images:

   ```php
   use Grupobahez\Imageoptimizer\Facades\ImageOptimizer;

   // ...
   ImageOptimizer::optimize(Storage::disk('public')->path('myimage.jpg'), [
       'disk' => 'public',
       'quality' => 70,
       'format' => 'webp',
       'thumbnails' => [
           'small' => [150, 150],
           'medium' => [300, 300],
           // ...
       ],
       'strategy' => 'cover', // fill | crop | fit | cover
       'output_folder' => 'new-folder', // Optional: Specify a custom output folder
       'output_file' => 'custom-name',  // Optional: Specify a custom base filename
   ]);
   ```
4. Optimize images from url
   
   ```php
   use Grupobahez\Imageoptimizer\Facades\ImageOptimizer;

   // ...
   ImageOptimizer::optimizeFromUrl('https://example.com/image.jpg', [
       'disk' => 'public',
       'quality' => 70,
       'format' => 'webp',
       'thumbnails' => [
           'small' => [150, 150],
           'medium' => [300, 300],
           // ...
       ],
       'strategy' => 'cover', // fill | crop | fit | cover
       'output_folder' => 'new-folder', // Optional: Specify a custom output folder
       'output_file' => 'custom-name',  // Optional: Specify a custom base filename
   ]);
   ```

## Default Configuration

The package includes a configuration file that you can overwrite by publishing it in your project. The default values are:

```php
return [
    'quality' => 80,
    'format' => 'webp',
    'strategy' => 'fit',
    'thumbnails' => [
        'small' => [150, 150],
        'medium' => [300, 300],
        'large' => [1024, 768],
    ],
];
```

## Features

- Image quality reduction.
- Conversion between formats (e.g., JPEG to WebP).
- Generation of multiple image sizes (*thumbnails*).
- Specify custom output folder and file names for the optimized images.
- Compatible with multiple resizing strategies:
   - **`fit`**: Maintains the aspect ratio; can handle `0` in width or height for proportional resizing.
   - **`fill`**: Maintains the aspect ratio and fills the entire frame; can handle `0` in width or height.
   - **`cover`**: Ensures the image covers the frame entirely, recropping from the center; can handle `0` in width or height.
   - **`crop`**: Crops the image to exact dimensions; both width and height must be specified (does not support `0`).

## Notes on Output Folder and Filename
- You can specify a custom output folder using the `output_folder` option.
- You can define a custom base filename using the `output_file` option.
- If `output_folder` is not provided, the folder of the original image will be used.
- If `output_file` is not provided, the name of the original image will be used as the base.

## Testing

The package includes unit tests with PHPUnit. You can run them using:

```bash
vendor/bin/phpunit
```

## License

This package is licensed under the [MIT License](LICENSE).
