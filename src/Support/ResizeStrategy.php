<?php

namespace Grupobahez\Imageoptimizer\Support;

use Intervention\Image\Image;

class ResizeStrategy
{
    public static function apply(Image $image, string $strategy, int $width = 0, int $height = 0): Image
    {
        $width = $width > 0 ? $width : null;
        $height = $height > 0 ? $height : null;

        switch ($strategy) {
            case ResizeStrategies::CROP:
                return self::applyCenterCrop($image, $width, $height);
            case ResizeStrategies::CROP_TOP_LEFT:
                return self::applyCornerCrop($image, $width, $height, 'top-left');
            case ResizeStrategies::CROP_TOP_RIGHT:
                return self::applyCornerCrop($image, $width, $height, 'top-right');
            case ResizeStrategies::CROP_BOTTOM_LEFT:
                return self::applyCornerCrop($image, $width, $height, 'bottom-left');
            case ResizeStrategies::CROP_BOTTOM_RIGHT:
                return self::applyCornerCrop($image, $width, $height, 'bottom-right');
            case ResizeStrategies::CROP_TOP:
                return self::applyEdgeCrop($image, $width, $height, 'top');
            case ResizeStrategies::CROP_RIGHT:
                return self::applyEdgeCrop($image, $width, $height, 'right');
            case ResizeStrategies::CROP_BOTTOM:
                return self::applyEdgeCrop($image, $width, $height, 'bottom');
            case ResizeStrategies::CROP_LEFT:
                return self::applyEdgeCrop($image, $width, $height, 'left');
            case ResizeStrategies::COVER:
                return self::applyCover($image, $width, $height);
            case ResizeStrategies::FILL:
                return $image->resize($width, $height, function ($constraint) {
                    $constraint->upsize();
                });
            case ResizeStrategies::FIT:
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

    protected static function applyEdgeCrop(Image $image, ?int $width, ?int $height, string $edge): Image
    {
        $width = $width ?? $image->width();
        $height = $height ?? $image->height();

        switch ($edge) {
            case 'top':
                $x = ($image->width() - $width) / 2;
                $y = 0;
                break;

            case 'right':
                $x = $image->width() - $width;
                $y = ($image->height() - $height) / 2;
                break;

            case 'bottom':
                $x = ($image->width() - $width) / 2;
                $y = $image->height() - $height;
                break;

            case 'left':
                $x = 0;
                $y = ($image->height() - $height) / 2;
                break;

            default:
                throw new \InvalidArgumentException("Invalid edge specified: {$edge}");
        }

        return $image->crop($width, $height, (int) $x, (int) $y);
    }
}
