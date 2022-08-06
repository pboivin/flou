<?php

namespace Pboivin\Flou\Tests;

use Pboivin\Flou\Image;
use Pboivin\Flou\ImageFile;
use Pboivin\Flou\ImageRender;
use Pboivin\Flou\Tests\Concerns\Mocking;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    use Mocking;

    public function test_can_access_source_and_cached()
    {
        $image = new Image($this->mockImageFile(), $this->mockImageFile());

        $this->assertTrue($image->source() instanceof ImageFile);
        $this->assertTrue($image->cached() instanceof ImageFile);
    }

    public function test_can_render_image()
    {
        $image = new Image($this->mockImageFile(), $this->mockImageFile());

        $this->assertTrue($image->render() instanceof ImageRender);
    }

    public function test_can_export_to_array()
    {
        ($source = $this->mockImageFile())
            ->shouldReceive('toArray')
            ->andReturn(['_source' => true]);

        ($cached = $this->mockImageFile())
            ->shouldReceive('toArray')
            ->andReturn(['_cached' => true]);

        $image = new Image($source, $cached);

        $this->assertEquals(
            [
                'source' => ['_source' => true],
                'cached' => ['_cached' => true],
            ],
            $image->toArray()
        );
    }
}
