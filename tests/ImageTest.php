<?php

namespace Pboivin\Flou\Tests;

use PHPUnit\Framework\TestCase;
use Pboivin\Flou\ImageRender;
use Pboivin\Flou\Tests\Helpers\Mocking;

class ImageTest extends TestCase
{
    use Mocking;

    public function test_can_create_image()
    {
        $image = $this->getImage();

        $image
            ->source()
            ->shouldReceive('url')
            ->andReturn('/source.jpg');

        $image
            ->cached()
            ->shouldReceive('url')
            ->andReturn('/cached.jpg');

        $this->assertEquals('/source.jpg', $image->source()->url());
        $this->assertEquals('/cached.jpg', $image->cached()->url());
    }

    public function test_can_render_image()
    {
        $image = $this->getImage();

        $this->assertTrue($image->render() instanceof ImageRender);
    }
}
