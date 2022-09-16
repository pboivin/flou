<?php

namespace Pboivin\Flou\Tests;

use PHPUnit\Framework\TestCase;
use Pboivin\Flou\Tests\Concerns\Mocking;

class IntegrationTest extends TestCase
{
    use Mocking;

    private $factory;

    protected function setUp(): void
    {
        $prepared = $this->prepareFactory();

        $prepared->serverMock->shouldReceive('makeImage')->andReturn('cached.jpg');

        $prepared->inspectorMock
            ->shouldReceive('getSize')
            ->andReturn(['width' => 123, 'height' => 123]);

        $this->factory = $prepared->factory;
    }

    public function test_can_handle_single_image()
    {
        $image = $this->factory->image('source.jpg');

        $output = $image->render()->img();

        $this->assertEquals(
            '<img class="lazyload" alt="" src="/images/cache/cached.jpg" data-src="/images/source/source.jpg" width="123" height="123">',
            $output
        );
    }

    public function test_can_handle_simple_imageset()
    {
        $set = $this->factory->imageSet([
            'image' => 'source.jpg',
            'widths' => [400],
        ]);

        $output = $set->render()->img();

        $this->assertEquals(
            '<img class="lazyload" alt="" src="/images/cache/cached.jpg" width="123" height="123" data-src="/images/cache/cached.jpg" data-srcset="/images/cache/cached.jpg" data-sizes="100vw">',
            $output
        );
    }

    public function test_can_handle_responsive_imageset()
    {
        $set = $this->factory->imageSet([
            'image' => 'source.jpg',
            'widths' => [400, 800, 1200],
            'sizes' => '66vw',
        ]);

        $output = $set->render()->img();

        $this->assertEquals(
            '<img class="lazyload" alt="" src="/images/cache/cached.jpg" width="123" height="123" data-src="/images/cache/cached.jpg" data-srcset="/images/cache/cached.jpg 400w, /images/cache/cached.jpg 800w, /images/cache/cached.jpg 1200w" data-sizes="66vw">',
            $output
        );
    }

    public function test_can_handle_multi_imageset()
    {
        $set = $this->factory->imageSet([
            [
                'image' => 'source1.jpg',
                'media' => '(max-width: 1023px)',
                'widths' => [400, 800],
            ],
            [
                'image' => 'source2.jpg',
                'media' => '(min-width: 1024px)',
                'widths' => [1200, 1600],
            ],
        ]);

        $output = $set->render()->picture();

        $this->assertEquals(
            '<picture><source media="(max-width: 1023px)" data-srcset="/images/cache/cached.jpg 400w, /images/cache/cached.jpg 800w"><source media="(min-width: 1024px)" data-srcset="/images/cache/cached.jpg 1200w, /images/cache/cached.jpg 1600w"><img class="lazyload" alt="" src="/images/cache/cached.jpg" data-src="/images/cache/cached.jpg" width="123" height="123"></picture>',
            $output
        );
    }
}
