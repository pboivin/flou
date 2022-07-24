<?php

namespace Pboivin\Flou\Tests;

use PHPUnit\Framework\TestCase;
use Pboivin\Flou\Image;
use Pboivin\Flou\ImageRender;
use Pboivin\Flou\Tests\Helpers\Mocking;

class ImageTest extends TestCase
{
    use Mocking;

    public function test_can_render_image()
    {
        $image = new Image($this->mockImageFile(), $this->mockImageFile());

        $this->assertTrue($image->render() instanceof ImageRender);
    }
}
