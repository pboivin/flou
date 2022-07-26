<?php

namespace Pboivin\Flou\Tests;

use Pboivin\Flou\ImageFileInspector;
use Pboivin\Flou\Tests\Helpers\Mocking;
use PHPUnit\Framework\TestCase;

class ImageFileInspectorTest extends TestCase
{
    use Mocking;

    public function test_can_inspect_size()
    {
        $inspector = new ImageFileInspector();

        $size = $inspector->getSize(__DIR__ . '/fixtures/square.jpg');

        $this->assertEquals(100, $size['width']);
        $this->assertEquals(100, $size['height']);
    }
}
