<?php

namespace Grupobahez\Imageoptimizer;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Grupobahez\Imageoptimizer\Support\CropStrategy;

class ImageOptimizerManager
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function optimize(string $absolutePath, array $options = []): array
    {
        $options = array_merge($this->config, $options);

        $destinationDisk = $options['disk'] ?? 'public'; // Disk de destino
        $quality = $options['quality'] ?? 80;
        $format = $options['format'] ?? 'webp';
        $thumbnails = $options['thumbnails'] ?? [];
        $strategy = $options['strategy'] ?? 'fit';

        // Usar output_folder si está definido, de lo contrario usar el root del disk de destino
        $outputFolder = $options['output_folder'] ?? '';
        $outputFile = $options['output_file'] ?? pathinfo($absolutePath, PATHINFO_FILENAME);

        // Generar la ruta del archivo optimizado
        $optimizedPath = ltrim($outputFolder . '/' . $outputFile . '.' . $format, '/');

        if (!file_exists($absolutePath)) {
            throw new \InvalidArgumentException("The file at path {$absolutePath} does not exist.");
        }

        $image = Image::make($absolutePath);

        if (isset($options['width']) && isset($options['height'])) {
            $width = (int) $options['width'];
            $height = (int) $options['height'];

            $image = CropStrategy::apply($image, $strategy, $width, $height);
        }

        // Guardar la imagen optimizada en el disk de destino
        Storage::disk($destinationDisk)->put(
            $optimizedPath,
            (string) $image->encode($format, $quality)
        );

        $generatedFiles = [
            'optimized' => $optimizedPath,
            'thumbnails' => [],
        ];

        foreach ($thumbnails as $key => [$thumbWidth, $thumbHeight]) {
            $thumbFilePath = ltrim($outputFolder . '/' . $outputFile . '_' . $key . '.' . $format, '/');

            $thumb = clone $image;
            $thumb = CropStrategy::apply($thumb, $strategy, $thumbWidth, $thumbHeight);

            // Guardar las miniaturas en el disk de destino
            Storage::disk($destinationDisk)->put(
                $thumbFilePath,
                (string) $thumb->encode($format, $quality)
            );

            $generatedFiles['thumbnails'][$key] = $thumbFilePath;
        }

        return $generatedFiles;
    }

    public function optimizeFromUrl(string $url, array $options = []): array
    {
        $options = array_merge($this->config, $options);

        $destinationDisk = $options['disk'] ?? 'public'; // Disk de destino
        $quality = $options['quality'] ?? 80;
        $format = $options['format'] ?? 'webp';
        $thumbnails = $options['thumbnails'] ?? [];
        $strategy = $options['strategy'] ?? 'fit';

        $outputFolder = $options['output_folder'] ?? '';
        $outputFile = $options['output_file'] ?? pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME);

        $optimizedPath = ltrim($outputFolder . '/' . $outputFile . '.' . $format, '/');

        if (!$this->isImageUrl($url)) {
            throw new \InvalidArgumentException("The URL does not point to a valid image: {$url}");
        }

        $imageContent = @file_get_contents($url);

        if ($imageContent === false) {
            throw new \InvalidArgumentException("Unable to fetch the image from the URL: {$url}");
        }

        $image = Image::make($imageContent);

        if (isset($options['width']) && isset($options['height'])) {
            $width = (int) $options['width'];
            $height = (int) $options['height'];

            $image = CropStrategy::apply($image, $strategy, $width, $height);
        }

        Storage::disk($destinationDisk)->put(
            $optimizedPath,
            (string) $image->encode($format, $quality)
        );

        $generatedFiles = [
            'optimized' => $optimizedPath,
            'thumbnails' => [],
        ];

        foreach ($thumbnails as $key => [$thumbWidth, $thumbHeight]) {
            $thumbFilePath = ltrim($outputFolder . '/' . $outputFile . '_' . $key . '.' . $format, '/');

            $thumb = clone $image;
            $thumb = CropStrategy::apply($thumb, $strategy, $thumbWidth, $thumbHeight);

            Storage::disk($destinationDisk)->put(
                $thumbFilePath,
                (string) $thumb->encode($format, $quality)
            );

            $generatedFiles['thumbnails'][$key] = $thumbFilePath;
        }

        return $generatedFiles;
    }

    protected function isImageUrl(string $url): bool
    {
        $headers = @get_headers($url, 1);
        if ($headers === false || !isset($headers['Content-Type'])) {
            return false;
        }

        $contentType = is_array($headers['Content-Type']) ? $headers['Content-Type'][0] : $headers['Content-Type'];

        return str_starts_with($contentType, 'image/');
    }
}
