<?php

namespace Pboivin\Flou\Tests;

use PHPUnit\Framework\TestCase;
use Pboivin\Flou\Image;
use Pboivin\Flou\ImageSetSizes;
use Pboivin\Flou\ImageSetSources;
use Pboivin\Flou\Tests\Helpers\Mocking;

class ImageSetTest extends TestCase
{
    use Mocking;

    public function test_can_create_imageset_from_simple_config()
    {
        $flou = $this->getFactory();

        $flou
            ->glideServer()
            ->shouldReceive('makeImage')
            ->andReturn('cached.jpg');

        $set = $flou->imageSet([
            'image' => 'source.jpg',
            'sources' => [
                'sm' => ['width' => 400],
                'md' => ['width' => 800],
                'lg' => ['width' => 1200],
            ],
        ]);

        $this->assertTrue(!!$set);
    }

    public function test_can_parse_image_and_sources()
    {
        //
    }

    // TODO add render test
}
