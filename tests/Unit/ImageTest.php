<?php

namespace Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;
use Pboivin\Flou\Image;
use Pboivin\Flou\ImageFile;

class ImageTest extends TestCase
{
    public function test_can_create_image()
    {
        $image = new Image(
            $source = $this->getImageFile(),
            $cached = $this->getImageFile()
        );

        $source->shouldReceive('url')->andReturn('/source.jpg');
        $cached->shouldReceive('url')->andReturn('/cached.jpg');

        $this->assertEquals('/source.jpg', $image->source()->url());
        $this->assertEquals('/cached.jpg', $image->cached()->url());
    }

    public function getImageFile()
    {
        /** @var ImageFile */
        $file = Mockery::mock(ImageFile::class);

        return $file;
    }
}
