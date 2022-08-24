<?php

namespace Pboivin\Flou\Tests;

use InvalidArgumentException;
use Pboivin\Flou\ImageSet;
use Pboivin\Flou\ImageSetRender;
use Pboivin\Flou\Tests\Concerns\Mocking;
use Pboivin\Flou\Tests\Fixtures\TestImageSetRender;
use PHPUnit\Framework\TestCase;

class ImageSetTest extends TestCase
{
    use Mocking;

    public function test_rejects_invalid_options()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid option 'test'.");

        $set = new ImageSet(['test' => 'test'], $this->mockFactory());
    }

    public function test_throws_exception_for_missing_sources()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'sources' is not set.");

        $set = new ImageSet(['image' => 'test.jpg'], $this->mockFactory());
    }

    public function test_throws_exception_for_missing_source_width()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Source is missing required 'width' option.");

        $set = new ImageSet(
            [
                'image' => 'source.jpg',
                'sources' => [
                    'sm' => [],
                    'md' => [],
                    'lg' => [],
                ],
            ],
            $this->mockFactory()
        );

        $set->data();
    }

    public function test_throws_exception_for_missing_image()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required 'image' option on source or imageset.");

        $set = new ImageSet(
            [
                'sources' => [
                    'sm' => ['width' => 400],
                    'md' => ['width' => 800],
                    'lg' => ['width' => 1200],
                ],
            ],
            $this->mockFactory()
        );

        $set->data();
    }

    public function test_has_default_sizes()
    {
        ($image = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($image);

        $set = new ImageSet(
            [
                'image' => 'source.jpg',
                'sources' => [
                    'sm' => ['width' => 400],
                    'md' => ['width' => 800],
                    'lg' => ['width' => 1200],
                ],
            ],
            $factory
        );

        $data = $set->data();

        $this->assertEquals('100vw', $data['sizes']);
    }

    public function test_can_use_custom_sizes()
    {
        ($image = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($image);

        $set = new ImageSet(
            [
                'image' => 'source.jpg',
                'sizes' => '(max-width: 800px) 100vw, 50vw',
                'sources' => [
                    'sm' => ['width' => 400],
                    'md' => ['width' => 800],
                    'lg' => ['width' => 1200],
                ],
            ],
            $factory
        );

        $data = $set->data();

        $this->assertEquals('(max-width: 800px) 100vw, 50vw', $data['sizes']);
    }

    public function test_can_prepare_sources_with_single_image()
    {
        ($image = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($image);

        $set = new ImageSet(
            [
                'image' => 'source.jpg',
                'sources' => [
                    'sm' => ['width' => 400],
                    'md' => ['width' => 800],
                    'lg' => ['width' => 1200],
                ],
            ],
            $factory
        );

        $data = $set->data();

        $this->assertEquals(3, count($data['srcset']));

        $source = $data['srcset'][0];
        $this->assertEquals('cached1.jpg', $source['image']->cached()->url());
        $this->assertEquals(400, $source['width']);

        $source = $data['srcset'][1];
        $this->assertEquals('cached2.jpg', $source['image']->cached()->url());
        $this->assertEquals(800, $source['width']);

        $source = $data['srcset'][2];
        $this->assertEquals('cached3.jpg', $source['image']->cached()->url());
        $this->assertEquals(1200, $source['width']);

        $this->assertEquals('cached3.jpg', $data['lqip']->cached()->url());
    }

    public function test_can_prepare_sources_with_multiple_images()
    {
        ($image = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        ($factory = $this->mockFactory())
            ->shouldReceive('image')
            ->with('source1.jpg', ['w' => 400])
            ->andReturn($image)
            ->shouldReceive('image')
            ->with('source2.jpg', ['w' => 800])
            ->andReturn($image)
            ->shouldReceive('image')
            ->with('source3.jpg', ['w' => 1200])
            ->andReturn($image);

        // lqip
        $factory
            ->shouldReceive('image')
            ->with('source3.jpg')
            ->andReturn($image);

        $set = new ImageSet(
            [
                'sources' => [
                    'sm' => [
                        'image' => 'source1.jpg',
                        'width' => 400,
                    ],
                    'md' => [
                        'image' => 'source2.jpg',
                        'width' => 800,
                    ],
                    'lg' => [
                        'image' => 'source3.jpg',
                        'width' => 1200,
                    ],
                ],
            ],
            $factory
        );

        $data = $set->data();

        $this->assertEquals(3, count($data['srcset']));

        $source = $data['srcset'][0];
        $this->assertEquals('cached1.jpg', $source['image']->cached()->url());
        $this->assertEquals(400, $source['width']);

        $source = $data['srcset'][1];
        $this->assertEquals('cached2.jpg', $source['image']->cached()->url());
        $this->assertEquals(800, $source['width']);

        $source = $data['srcset'][2];
        $this->assertEquals('cached3.jpg', $source['image']->cached()->url());
        $this->assertEquals(1200, $source['width']);

        $this->assertEquals('cached3.jpg', $data['lqip']->cached()->url());
    }

    public function test_can_render_imageset()
    {
        ($image = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($image);

        $set = new ImageSet(
            [
                'image' => 'source.jpg',
                'sources' => [
                    'sm' => ['width' => 400],
                    'md' => ['width' => 800],
                    'lg' => ['width' => 1200],
                ],
            ],
            $factory
        );

        $this->assertTrue($set->render() instanceof ImageSetRender);
    }

    public function test_can_configure_render_class()
    {
        ($image = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($image);

        $set = new ImageSet(
            [
                'image' => 'source.jpg',
                'sources' => [
                    'sm' => ['width' => 400],
                    'md' => ['width' => 800],
                    'lg' => ['width' => 1200],
                ],
            ],
            $factory
        );

        $set->setRenderClass(TestImageSetRender::class);

        $this->assertTrue($set->render() instanceof TestImageSetRender);
    }

    public function test_can_export_to_array()
    {
        ($image = $this->getImage())
            ->cached()
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        $image
            ->cached()
            ->shouldReceive('toArray')
            ->andReturn(['_cached' => true]);

        $image
            ->source()
            ->shouldReceive('toArray')
            ->andReturn(['_source' => true]);

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($image);

        $set = new ImageSet(
            [
                'image' => 'source.jpg',
                'sources' => [['width' => 400], ['width' => 800], ['width' => 1200]],
            ],
            $factory
        );

        $imageData = [
            'source' => ['_source' => true],
            'cached' => ['_cached' => true],
        ];

        $this->assertEquals(
            [
                'sizes' => '100vw',
                'srcset' => [
                    ['image' => $imageData, 'width' => 400],
                    ['image' => $imageData, 'width' => 800],
                    ['image' => $imageData, 'width' => 1200],
                ],
                'lqip' => $imageData,
            ],
            $set->toArray()
        );
    }
}
