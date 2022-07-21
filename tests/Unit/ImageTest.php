<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Pboivin\Flou\Image;

class ImageTest extends TestCase
{
    public function test_can_create_image()
    {
        $image = new Image('/source/source.jpg', '/cache/cached.jpg');

        $this->assertEquals('/cache/cached.jpg', $image->url());
        $this->assertEquals('/source/source.jpg', $image->sourceUrl());
    }
}
