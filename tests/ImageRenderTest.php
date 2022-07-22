<?php

namespace Pboivin\Flou\Tests;

use PHPUnit\Framework\TestCase;
use Pboivin\Flou\ImageRender;
use Pboivin\Flou\Tests\Helpers\Mocking;

class ImageRenderTest extends TestCase
{
    use Mocking;

    public function test_can_render_img()
    {
        $imageRender = new ImageRender(($image = $this->getImage()));

        $image
            ->source()
            ->shouldReceive('url')
            ->andReturn('/source.jpg');
        $image
            ->source()
            ->shouldReceive('width')
            ->andReturn(800);
        $image
            ->source()
            ->shouldReceive('height')
            ->andReturn(600);

        $image
            ->cached()
            ->shouldReceive('url')
            ->andReturn('/cached.jpg');

        $output = $imageRender->img([
            'class' => 'test',
            'alt' => 'This is a test',
            'data-custom' => 'custom',
        ]);

        $this->assertEquals(
            '<img class="lazyload test" alt="This is a test" data-custom="custom" src="/cached.jpg" data-src="/source.jpg" width="800" height="600">',
            $output
        );
    }
}
