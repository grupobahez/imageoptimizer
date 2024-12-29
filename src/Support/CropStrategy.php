<?php

namespace Grupobahez\Imageoptimizer\Support;

use Intervention\Image\Image;

class CropStrategy
{
    public static function apply(Image $image, string $strategy, int $width = 0, int $height = 0): Image
    {
        $width = $width > 0 ? $width : null;
        $height = $height > 0 ? $height : null;

        switch ($strategy) {
            case 'crop':
                return self::applyCenterCrop($image, $width, $height);
            case 'crop-top-left':
                return self::applyCornerCrop($image, $width, $height, 'top-left');
            case 'crop-top-right':
                return self::applyCornerCrop($image, $width, $height, 'top-right');
            case 'crop-bottom-left':
                return self::applyCornerCrop($image, $width, $height, 'bottom-left');
            case 'crop-bottom-right':
                return self::applyCornerCrop($image, $width, $height, 'bottom-right');
            case 'cover':
                return self::applyCover($image, $width, $height);
            case 'fill':
                return $image->resize($width, $height, function ($constraint) {
                    $constraint->upsize();
                });
            case 'fit':
            default:
                return $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
        }
    }

    protected static function applyCover(Image $image, ?int $targetWidth, ?int $targetHeight): Image
    {
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        if (! $targetWidth) {
            $targetWidth = (int) round($originalWidth * ($targetHeight / $originalHeight));
        }

        if (! $targetHeight) {
            $targetHeight = (int) round($originalHeight * ($targetWidth / $originalWidth));
        }

        $scale = max(
            $targetWidth / $originalWidth,
            $targetHeight / $originalHeight
        );

        $resizeWidth = (int) ceil($originalWidth * $scale);
        $resizeHeight = (int) ceil($originalHeight * $scale);

        $image->resize($resizeWidth, $resizeHeight);

        return $image->crop($targetWidth, $targetHeight);
    }

    protected static function applyCenterCrop(Image $image, ?int $width, ?int $height): Image
    {
        $width = $width ?? $image->width();
        $height = $height ?? $image->height();

        return $image->crop($width, $height);
    }

    protected static function applyCornerCrop(Image $image, ?int $width, ?int $height, string $corner): Image
    {
        $width = $width ?? $image->width();
        $height = $height ?? $image->height();

        switch ($corner) {
            case 'top-left':
                $x = 0;
                $y = 0;
                break;

            case 'top-right':
                $x = $image->width() - $width;
                $y = 0;
                break;

            case 'bottom-left':
                $x = 0;
                $y = $image->height() - $height;
                break;

            case 'bottom-right':
                $x = $image->width() - $width;
                $y = $image->height() - $height;
                break;

            default:
                throw new \InvalidArgumentException("Invalid corner specified: {$corner}");
        }

        return $image->crop($width, $height, $x, $y);
    }
}
