<?php

namespace Grupobahez\Imageoptimizer;

use Grupobahez\Imageoptimizer\Support\CropStrategy;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use InvalidArgumentException;

class ImageOptimizerManager
{
    /**
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array{
     *     optimized: string,
     *     thumbnails: array<string, string>
     * }
     *
     * @throws InvalidArgumentException
     */
    public function optimize(string $absolutePath, array $options = []): array
    {
        if (! file_exists($absolutePath)) {
            throw new InvalidArgumentException("The file at path {$absolutePath} does not exist.");
        }

        $image = Image::make($absolutePath);

        $options = array_merge($this->config, $options);

        return $this->processImage($image, $options, $absolutePath);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array{
     *     optimized: string,
     *     thumbnails: array<string, string>
     * }
     *
     * @throws InvalidArgumentException
     */
    public function optimizeFromUrl(string $url, array $options = []): array
    {
        if (! $this->isImageUrl($url)) {
            throw new InvalidArgumentException("The URL does not point to a valid image: {$url}");
        }

        $imageContent = @file_get_contents($url);

        if ($imageContent === false) {
            throw new InvalidArgumentException("Unable to fetch the image from the URL: {$url}");
        }

        $image = Image::make($imageContent);

        $options = array_merge($this->config, $options);

        return $this->processImage($image, $options, $url);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array{
     *     optimized: string,
     *     thumbnails: array<string, string>
     * }
     */
    protected function processImage(\Intervention\Image\Image $image, array $options, string $source): array
    {
        $destinationDisk = $options['disk'] ?? 'public';
        $quality = $options['quality'] ?? 80;
        $format = $options['format'] ?? 'webp';
        $thumbnails = $options['thumbnails'] ?? [];
        $strategy = $options['strategy'] ?? 'fit';
        $outputFolder = $options['output_folder'] ?? '';

        $outputFile = $this->resolveOutputFileName($options, $source);
        $optimizedPath = ltrim($outputFolder.'/'.$outputFile.'.'.$format, '/');

        if (isset($options['width']) && isset($options['height'])) {
            $width = (int) $options['width'];
            $height = (int) $options['height'];
            $image = CropStrategy::apply($image, $strategy, $width, $height);
        }

        Storage::disk($destinationDisk)->put(
            $optimizedPath,
            (string) $image->encode($format, $quality)
        );

        return [
            'optimized' => $optimizedPath,
            'thumbnails' => $this->generateThumbnails($image, $thumbnails, $options, $outputFile),
        ];
    }

    /**
     * Genera mi
     *
     * @param  array<string, array{int,int}>  $thumbnails
     * @param  array<string, mixed>  $options
     * @return array<string, string>
     */
    protected function generateThumbnails(
        \Intervention\Image\Image $image,
        array $thumbnails,
        array $options,
        string $baseFileName,
    ): array {
        $destinationDisk = $options['disk'] ?? 'public';
        $quality = $options['quality'] ?? 80;
        $format = $options['format'] ?? 'webp';
        $strategy = $options['strategy'] ?? 'fit';
        $outputFolder = $options['output_folder'] ?? '';

        $thumbnailPaths = [];

        foreach ($thumbnails as $key => [$thumbWidth, $thumbHeight]) {
            $thumbFilePath = ltrim($outputFolder.'/'.$baseFileName.'_'.$key.'.'.$format, '/');

            $thumb = clone $image;
            $thumb = CropStrategy::apply($thumb, $strategy, $thumbWidth, $thumbHeight);

            Storage::disk($destinationDisk)->put(
                $thumbFilePath,
                (string) $thumb->encode($format, $quality)
            );

            $thumbnailPaths[$key] = $thumbFilePath;
        }

        return $thumbnailPaths;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    protected function resolveOutputFileName(array $options, string $source): string
    {
        if (isset($options['output_file']) && $options['output_file'] !== '') {
            return $options['output_file'];
        }

        if (filter_var($source, FILTER_VALIDATE_URL)) {
            $pathInfo = parse_url($source, PHP_URL_PATH);

            return $pathInfo ? pathinfo($pathInfo, PATHINFO_FILENAME) : 'default_filename';
        }

        return pathinfo($source, PATHINFO_FILENAME);
    }

    protected function isImageUrl(string $url): bool
    {
        $headers = @get_headers($url, true);
        if ($headers === false || ! isset($headers['Content-Type'])) {
            return false;
        }

        $contentType = is_array($headers['Content-Type'])
            ? $headers['Content-Type'][0]
            : $headers['Content-Type'];

        return str_starts_with($contentType, 'image/');
    }
}
