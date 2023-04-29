<?php

namespace Pboivin\Flou\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Pboivin\Flou\ImageSetConfig;

class ImageSetConfigTest extends TestCase
{
    public function test_throws_exception_for_missing_image()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid ImageSet configuration — missing image.');

        new ImageSetConfig([]);
    }

    public function test_throws_exception_for_missing_widths()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required 'widths' argument for single image.");

        new ImageSetConfig(['image' => 'test.jpg']);
    }

    public function test_throws_exception_for_missing_media()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required 'media' argument for multiple images.");

        new ImageSetConfig([
            [
                'image' => '01.jpg',
                'widths' => [400, 800],
            ],
            [
                'image' => '02.jpg',
                'widths' => [1200, 1600],
            ],
        ]);
    }

    public function test_accepts_single_image_config()
    {
        $imageSetConfig = new ImageSetConfig([
            'image' => 'test.jpg',
            'widths' => [400, 800, 1200],
        ]);

        $this->assertEquals(
            [
                [
                    'image' => 'test.jpg',
                    'widths' => [400, 800, 1200],
                ],
            ],
            $imageSetConfig->get()
        );
    }

    public function test_accepts_multi_image_config()
    {
        $imageSetConfig = new ImageSetConfig([
            [
                'image' => '01.jpg',
                'widths' => [400, 800],
                'media' => '(max-width: 1023px)',
            ],
            [
                'image' => '02.jpg',
                'widths' => [1200, 1600],
                'media' => '(min-width: 1024px)',
            ],
        ]);

        $this->assertEquals(
            [
                [
                    'image' => '01.jpg',
                    'widths' => [400, 800],
                    'media' => '(max-width: 1023px)',
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1200, 1600],
                    'media' => '(min-width: 1024px)',
                ],
            ],
            $imageSetConfig->get()
        );
    }

    public function test_converts_single_image_config_with_formats()
    {
        $imageSetConfig = new ImageSetConfig([
            'image' => 'test.jpg',
            'widths' => [400, 800, 1200],
            'formats' => ['webp', 'jpg'],
        ]);

        $this->assertEquals(
            [
                [
                    'image' => 'test.jpg',
                    'widths' => [400, 800, 1200],
                    'format' => 'webp',
                ],
                [
                    'image' => 'test.jpg',
                    'widths' => [400, 800, 1200],
                    'format' => 'jpg',
                ],
            ],
            $imageSetConfig->get()
        );
    }

    public function test_converts_multi_image_config_with_formats()
    {
        $imageSetConfig = new ImageSetConfig([
            [
                'image' => '01.jpg',
                'widths' => [400, 800],
                'media' => '(max-width: 1023px)',
                'formats' => ['webp', 'jpg'],
            ],
            [
                'image' => '02.jpg',
                'widths' => [1200, 1600],
                'media' => '(min-width: 1024px)',
                'formats' => ['webp', 'jpg'],
            ],
        ]);

        $this->assertEquals(
            [
                [
                    'image' => '01.jpg',
                    'widths' => [400, 800],
                    'media' => '(max-width: 1023px)',
                    'format' => 'webp',
                ],
                [
                    'image' => '01.jpg',
                    'widths' => [400, 800],
                    'media' => '(max-width: 1023px)',
                    'format' => 'jpg',
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1200, 1600],
                    'media' => '(min-width: 1024px)',
                    'format' => 'webp',
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1200, 1600],
                    'media' => '(min-width: 1024px)',
                    'format' => 'jpg',
                ],
            ],
            $imageSetConfig->get()
        );
    }
}
