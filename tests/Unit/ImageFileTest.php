<?php

namespace Pboivin\Flou\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Pboivin\Flou\ImageFile;
use Pboivin\Flou\Tests\Helpers\Mocking;

class ImageFileTest extends TestCase
{
    use Mocking;

    public function test_can_create_image_file()
    {
        $imageFile = new ImageFile(
            'my-image.jpg',
            '/path/to/my-image.jpg',
            '/url/to/my-image.jpg',
            ($inspector = $this->mockInspector())
        );

        $inspector->shouldReceive('getSize')->andReturn(['width' => 123, 'height' => 123]);

        $this->assertEquals('my-image.jpg', $imageFile->fileName());
        $this->assertEquals('/path/to/my-image.jpg', $imageFile->path());
        $this->assertEquals('/url/to/my-image.jpg', $imageFile->url());
        $this->assertEquals(123, $imageFile->width());
        $this->assertEquals(123, $imageFile->height());
    }
}
