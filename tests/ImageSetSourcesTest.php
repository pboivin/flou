<?php

namespace Pboivin\Flou\Tests;

use PHPUnit\Framework\TestCase;
use Pboivin\Flou\ImageSetSources;
use Pboivin\Flou\Tests\Concerns\Mocking;

class ImageSetSourcesTest extends TestCase
{
    use Mocking;

    public function test_handle_single_image()
    {
        ($image = $this->mockImage())->shouldReceive('toArray')->andReturn(['_image_array_']);

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($image);

        $sources = new ImageSetSources(
            [
                [
                    'image' => 'test.jpg',
                    'widths' => [400, 800, 1200],
                ],
            ],
            $factory
        );

        $data = $sources->toArray()[0];

        $this->assertEquals('test.jpg', $data['image']);
        $this->assertEquals([400, 800, 1200], $data['widths']);
        $this->assertSrcSet([400, 800, 1200], $data['srcset']);
    }

    public function test_handle_multiple_images()
    {
        ($image = $this->mockImage())->shouldReceive('toArray')->andReturn(['_image_array_']);

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($image);

        $sources = new ImageSetSources(
            [
                [
                    'image' => '01.jpg',
                    'widths' => [400, 800],
                    'media' => '(max-width: 1023px)',
                ],
                [
                    'image' => '02.jpg',
                    'widths' => [1200, 1600],
                    'media' => '(min-width: 1024px)',
                ],
            ],
            $factory
        );

        $data = $sources->toArray()[0];
        $this->assertEquals('01.jpg', $data['image']);
        $this->assertEquals([400, 800], $data['widths']);
        $this->assertSrcSet([400, 800], $data['srcset']);
        $this->assertEquals('(max-width: 1023px)', $data['media']);

        $data = $sources->toArray()[1];
        $this->assertEquals('02.jpg', $data['image']);
        $this->assertEquals([1200, 1600], $data['widths']);
        $this->assertSrcSet([1200, 1600], $data['srcset']);
        $this->assertEquals('(min-width: 1024px)', $data['media']);
    }

    public function test_preserves_optional_sizes_property()
    {
        ($image = $this->mockImage())->shouldReceive('toArray')->andReturn(['_image_array_']);

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($image);

        $sources = new ImageSetSources(
            [
                [
                    'image' => 'test.jpg',
                    'widths' => [400, 800, 1200],
                    'sizes' => '66vw',
                ],
            ],
            $factory
        );

        $data = $sources->toArray()[0];

        $this->assertEquals('66vw', $data['sizes']);
    }

    public function test_handle_image_formats()
    {
        $this->expectFormats($factory = $this->mockFactory(), [
            [
                'image' => 'test.jpg',
                'widths' => [400, 800, 1200],
                'format' => 'webp',
            ],
            [
                'image' => 'test.jpg',
                'widths' => [400, 800, 1200],
                'format' => 'jpg',
            ],
        ]);

        $sources = new ImageSetSources(
            [
                [
                    'image' => 'test.jpg',
                    'widths' => [400, 800, 1200],
                    'format' => 'webp',
                ],
                [
                    'image' => 'test.jpg',
                    'widths' => [400, 800, 1200],
                    'format' => 'jpg',
                ],
            ],
            $factory
        );

        $data = $sources->toArray();
        $this->assertEquals(2, count($data));

        $this->assertEquals('test.jpg', $data[0]['image']);
        $this->assertEquals([400, 800, 1200], $data[0]['widths']);
        $this->assertSrcSet([400, 800, 1200], $data[0]['srcset']);

        $this->assertEquals('test.jpg', $data[1]['image']);
        $this->assertEquals([400, 800, 1200], $data[1]['widths']);
        $this->assertSrcSet([400, 800, 1200], $data[1]['srcset']);
    }

    private function assertSrcSet($widths, $data)
    {
        $this->assertEquals(count($widths), count($data));

        for ($i = 0; $i < count($widths); $i++) {
            $this->assertEquals(
                [
                    'image' => ['_image_array_'],
                    'width' => $widths[$i],
                ],
                $data[$i]
            );
        }
    }
    private function expectFormats($factory, $config)
    {
        ($image = $this->mockImage())->shouldReceive('toArray')->andReturn(['_image_array_']);

        foreach ($config as $item) {
            foreach ($item['widths'] as $width) {
                $factory
                    ->shouldReceive('image')
                    ->with($item['image'], [
                        'w' => $width,
                        'fm' => $item['format'],
                    ])
                    ->andReturn($image);
            }
        }
    }
}
