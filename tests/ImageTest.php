<?php

namespace Pboivin\Flou\Tests;

use Pboivin\Flou\Image;
use Pboivin\Flou\ImageFile;
use Pboivin\Flou\ImageRender;
use Pboivin\Flou\Tests\Helpers\Mocking;
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
        $image = new Image($this->mockImageFile(), $this->mockImageFile());

        // @phpstan-ignore-next-line
        $image
            ->source()
            ->shouldReceive('toArray')
            ->andReturn(['(source)']);

        // @phpstan-ignore-next-line
        $image
            ->cached()
            ->shouldReceive('toArray')
            ->andReturn(['(cached)']);

        $this->assertEquals(
            [
                'source' => ['(source)'],
                'cached' => ['(cached)'],
            ],
            $image->toArray()
        );
    }
}
