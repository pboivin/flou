<?php

namespace Tests\Unit;

use Pboivin\Flou\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function test_start_fresh()
    {
        $this->assertEquals('fresh', (new Image)->start());
    }
}
