<?php

namespace Grupobahez\Imageoptimizer\Tests;

use Grupobahez\Imageoptimizer\ImageOptimizerManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class ImageOptimizerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('test');
    }

    public function test_it_can_optimize_and_generate_thumbnails_with_defaults(): void
    {
        $config = [
            'quality' => 80,
            'format' => 'webp',
            'strategy' => 'cover',
        ];
        $imageOptimizer = new ImageOptimizerManager($config);

        $originalImage = Image::canvas(800, 600, '#ff0000')->encode('jpg', 100);
        Storage::disk('test')->put('images/original.jpg', (string) $originalImage);

        $result = $imageOptimizer->optimize(Storage::disk('test')->path('images/original.jpg'), [
            'disk' => 'test',
            'quality' => 50,
            'format' => 'webp',
            'thumbnails' => [
                'small' => [100, 100],
                'medium' => [200, 200],
            ],
            'strategy' => 'cover',
        ]);

        $this->assertArrayHasKey('optimized', $result);
        $this->assertArrayHasKey('thumbnails', $result);
        $this->assertTrue(Storage::disk('test')->exists('original.webp'));
        $this->assertTrue(Storage::disk('test')->exists('original_small.webp'));
        $this->assertTrue(Storage::disk('test')->exists('original_medium.webp'));
    }

    public function test_it_can_optimize_and_generate_thumbnails_in_a_specific_folder(): void
    {
        $config = [
            'quality' => 80,
            'format' => 'webp',
            'strategy' => 'cover',
        ];
        $imageOptimizer = new ImageOptimizerManager($config);

        $originalImage = Image::canvas(800, 600, '#ff0000')->encode('jpg', 100);
        Storage::disk('test')->put('images/original.jpg', (string) $originalImage);

        $result = $imageOptimizer->optimize(Storage::disk('test')->path('images/original.jpg'), [
            'disk' => 'test',
            'quality' => 50,
            'format' => 'webp',
            'output_folder' => 'new-folder',
            'thumbnails' => [
                'small' => [100, 100],
                'medium' => [200, 200],
            ],
            'strategy' => 'cover',
        ]);

        $this->assertTrue(Storage::disk('test')->exists('new-folder/original.webp'));
        $this->assertTrue(Storage::disk('test')->exists('new-folder/original_small.webp'));
        $this->assertTrue(Storage::disk('test')->exists('new-folder/original_medium.webp'));
    }

    public function test_it_can_optimize_and_generate_thumbnails_with_a_specific_file_name(): void
    {
        $config = [
            'quality' => 80,
            'format' => 'webp',
            'strategy' => 'cover',
        ];
        $imageOptimizer = new ImageOptimizerManager($config);

        $originalImage = Image::canvas(800, 600, '#ff0000')->encode('jpg', 100);
        Storage::disk('test')->put('images/original.jpg', (string) $originalImage);

        $result = $imageOptimizer->optimize(Storage::disk('test')->path('images/original.jpg'), [
            'disk' => 'test',
            'quality' => 50,
            'format' => 'webp',
            'output_file' => 'custom-name',
            'thumbnails' => [
                'small' => [100, 100],
                'medium' => [200, 200],
            ],
            'strategy' => 'cover',
        ]);

        $this->assertTrue(Storage::disk('test')->exists('custom-name.webp'));
        $this->assertTrue(Storage::disk('test')->exists('custom-name_small.webp'));
        $this->assertTrue(Storage::disk('test')->exists('custom-name_medium.webp'));
    }

    public function test_it_can_optimize_and_generate_thumbnails_in_a_specific_folder_with_a_specific_file_name(): void
    {
        $config = [
            'quality' => 80,
            'format' => 'webp',
            'strategy' => 'cover',
        ];
        $imageOptimizer = new ImageOptimizerManager($config);

        $originalImage = Image::canvas(800, 600, '#ff0000')->encode('jpg', 100);
        Storage::disk('test')->put('images/original.jpg', (string) $originalImage);

        $result = $imageOptimizer->optimize(Storage::disk('test')->path('images/original.jpg'), [
            'disk' => 'test',
            'quality' => 50,
            'format' => 'webp',
            'output_folder' => 'new-folder',
            'output_file' => 'custom-name',
            'thumbnails' => [
                'small' => [100, 100],
                'medium' => [200, 200],
            ],
            'strategy' => 'cover',
        ]);

        $this->assertTrue(Storage::disk('test')->exists('new-folder/custom-name.webp'));
        $this->assertTrue(Storage::disk('test')->exists('new-folder/custom-name_small.webp'));
        $this->assertTrue(Storage::disk('test')->exists('new-folder/custom-name_medium.webp'));
    }

    public function test_it_can_optimize_and_generate_thumbnails_with_relative_dimensions(): void
    {
        $config = [
            'quality' => 80,
            'format' => 'webp',
            'strategy' => 'cover',
        ];
        $imageOptimizer = new ImageOptimizerManager($config);

        $originalImage = Image::canvas(800, 600, '#ff0000')->encode('jpg', 100);
        Storage::disk('test')->put('images/original.jpg', (string) $originalImage);

        $result = $imageOptimizer->optimize(Storage::disk('test')->path('images/original.jpg'), [
            'disk' => 'test',
            'quality' => 50,
            'format' => 'webp',
            'thumbnails' => [
                'small' => [400, 0],
                'medium' => [0, 300],
            ],
            'strategy' => 'cover',
        ]);

        $this->assertArrayHasKey('optimized', $result);
        $this->assertArrayHasKey('thumbnails', $result);

        $this->assertTrue(Storage::disk('test')->exists('original.webp'));
        $this->assertTrue(Storage::disk('test')->exists('original_small.webp'));
        $this->assertTrue(Storage::disk('test')->exists('original_medium.webp'));

        $smallImage = Image::make(Storage::disk('test')->get('original_small.webp'));
        $mediumImage = Image::make(Storage::disk('test')->get('original_medium.webp'));

        $this->assertEquals(400, $smallImage->width());
        $this->assertEquals(300, $smallImage->height());

        $this->assertEquals(400, $mediumImage->width());
        $this->assertEquals(300, $mediumImage->height());
    }

    public function test_it_can_optimize_from_url_with_defaults(): void
    {
        $config = [
            'quality' => 80,
            'format' => 'webp',
            'strategy' => 'cover',
        ];
        $imageOptimizer = new ImageOptimizerManager($config);

        $testImageUrl = 'https://via.placeholder.com/800x600.jpg';

        $result = $imageOptimizer->optimizeFromUrl($testImageUrl, [
            'disk' => 'test',
            'quality' => 50,
            'format' => 'webp',
            'thumbnails' => [
                'small' => [100, 100],
                'medium' => [200, 200],
            ],
            'strategy' => 'cover',
        ]);

        $this->assertArrayHasKey('optimized', $result);
        $this->assertArrayHasKey('thumbnails', $result);

        $this->assertTrue(Storage::disk('test')->exists($result['optimized']));

        foreach ($result['thumbnails'] as $thumbnailPath) {
            $this->assertTrue(Storage::disk('test')->exists($thumbnailPath));
        }
    }
}
