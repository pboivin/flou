<?php

namespace Pboivin\Flou\Tests;

use Pboivin\Flou\ImageSetConfig;
use PHPUnit\Framework\TestCase;

class ImageSetConfigTest extends TestCase
{
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

    public function test_converts_legacy_single_image_config()
    {
        $imageSetConfig = new ImageSetConfig([
            'image' => 'test.jpg',
            'sources' => [['width' => 400], ['width' => 800], ['width' => 1200]],
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

    public function test_converts_legacy_multi_image_config()
    {
        $imageSetConfig = new ImageSetConfig([
            'sources' => [
                [
                    'image' => '01.jpg',
                    'width' => 1023,
                    'media' => '(max-width: 1023px)',
                ],
                [
                    'image' => '02.jpg',
                    'width' => 1024,
                    'media' => '(min-width: 1024px)',
                ],
            ],
        ]);

        $this->assertEquals(
            [
                [
                    'image' => '01.jpg',
                    'widths' => [1023],
                    'media' => '(max-width: 1023px)',
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1024],
                    'media' => '(min-width: 1024px)',
                ],
            ],
            $imageSetConfig->get()
        );
    }
}
