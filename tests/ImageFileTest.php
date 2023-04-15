<?php

namespace Pboivin\Flou\Tests;

use Pboivin\Flou\ImageFile;
use Pboivin\Flou\Tests\Concerns\Mocking;
use PHPUnit\Framework\TestCase;

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
        $this->assertEquals(1, $imageFile->ratio());
    }

    public function test_can_export_to_array()
    {
        $imageFile = new ImageFile(
            'my-image.jpg',
            '/path/to/my-image.jpg',
            '/url/to/my-image.jpg',
            ($inspector = $this->mockInspector())
        );

        $inspector->shouldReceive('getSize')->andReturn(['width' => 123, 'height' => 123]);

        $this->assertEquals(
            [
                'fileName' => 'my-image.jpg',
                'path' => '/path/to/my-image.jpg',
                'url' => '/url/to/my-image.jpg',
                'width' => 123,
                'height' => 123,
                'ratio' => 1,
            ],
            $imageFile->toArray()
        );
    }

    public function test_can_encode_to_base64()
    {
        $imageFile = new ImageFile(
            'my-image.jpg',
            '/path/to/my-image.jpg',
            '/url/to/my-image.jpg',
            ($inspector = $this->mockInspector())
        );

        $inspector->shouldReceive('base64Encode')->andReturn('_some_base64_encoded_string_');

        $this->assertEquals(
            '_some_base64_encoded_string_',
            $imageFile->toBase64String()
        );
    }

    public function test_can_create_image_file_without_inspector()
    {
        $imageFile = new ImageFile(
            'my-image.jpg',
            '/path/to/my-image.jpg',
            '/url/to/my-image.jpg',
        );

        $this->assertEquals('my-image.jpg', $imageFile->fileName());
        $this->assertEquals('/path/to/my-image.jpg', $imageFile->path());
        $this->assertEquals('/url/to/my-image.jpg', $imageFile->url());
        $this->assertEquals(0, $imageFile->width());
        $this->assertEquals(0, $imageFile->height());
        $this->assertEquals(0.0, $imageFile->ratio());
        $this->assertEquals(ImageFile::BASE64_IMAGE_FALLBACK, $imageFile->toBase64String());
    }
}
