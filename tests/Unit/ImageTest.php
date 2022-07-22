<?php

namespace Pboivin\Flou\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Pboivin\Flou\Image;
use Pboivin\Flou\Tests\Helpers\Mocking;

class ImageTest extends TestCase
{
    use Mocking;

    public function test_can_create_image()
    {
        $image = new Image(($source = $this->mockImageFile()), ($cached = $this->mockImageFile()));

        $source->shouldReceive('url')->andReturn('/source.jpg');
        $cached->shouldReceive('url')->andReturn('/cached.jpg');

        $this->assertEquals('/source.jpg', $image->source()->url());
        $this->assertEquals('/cached.jpg', $image->cached()->url());
    }
}
