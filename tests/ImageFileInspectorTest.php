<?php

namespace Pboivin\Flou\Tests;

use Pboivin\Flou\ImageFileInspector;
use PHPUnit\Framework\TestCase;

class ImageFileInspectorTest extends TestCase
{
    public function test_can_inspect_size()
    {
        $inspector = new ImageFileInspector();

        $size = $inspector->getSize(__DIR__ . '/Fixtures/source/square.jpg');

        $this->assertEquals(100, $size['width']);
        $this->assertEquals(100, $size['height']);
    }

    public function test_can_encode_to_base64()
    {
        $inspector = new ImageFileInspector();

        $base64 = $inspector->base64Encode(__DIR__ . '/Fixtures/source/pixel.gif');

        $this->assertEquals(
            'data:image/gif;base64,R0lGODlhAQABAIABAAAAAP///yH+EUNyZWF0ZWQgd2l0aCBHSU1QACwAAAAAAQABAAACAkQBADs=',
            $base64
        );
    }
}
