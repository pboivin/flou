<?php

namespace Pboivin\Flou\Tests;

use Pboivin\Flou\ImageSet;
use Pboivin\Flou\ImageSetRender;
use Pboivin\Flou\Tests\Concerns\Mocking;
use Pboivin\Flou\Tests\Fixtures\TestImageSetRender;
use PHPUnit\Framework\TestCase;

class ImageSetTest extends TestCase
{
    use Mocking;

    public function test_can_prepare_sources_with_single_image()
    {
        $prepared = $this->prepareImage();

        $prepared->cachedMock
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($prepared->image);

        $set = new ImageSet(
            [
                'image' => 'source.jpg',
                'widths' => [400, 800, 1200],
            ],
            $factory
        );

        $data = $set->data();

        $this->assertEquals(1, count($data['sources']));

        $srcset = $data['sources'][0]['srcset'];

        $source = $srcset[0];
        $this->assertEquals('cached1.jpg', $source['image']->cached()->url());
        $this->assertEquals(400, $source['width']);

        $source = $srcset[1];
        $this->assertEquals('cached2.jpg', $source['image']->cached()->url());
        $this->assertEquals(800, $source['width']);

        $source = $srcset[2];
        $this->assertEquals('cached3.jpg', $source['image']->cached()->url());
        $this->assertEquals(1200, $source['width']);

        $this->assertEquals('cached3.jpg', $data['lqip']->cached()->url());
    }

    public function test_can_prepare_sources_with_multiple_images()
    {
        $prepared = $this->prepareImage();

        $prepared->cachedMock
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg', 'cached4.jpg');

        ($factory = $this->mockFactory())
            ->shouldReceive('image')
            ->with('source1.jpg', ['w' => 400])
            ->andReturn($prepared->image)
            ->shouldReceive('image')
            ->with('source1.jpg', ['w' => 800])
            ->andReturn($prepared->image)
            ->shouldReceive('image')
            ->with('source2.jpg', ['w' => 1200])
            ->andReturn($prepared->image)
            ->shouldReceive('image')
            ->with('source2.jpg', ['w' => 1600])
            ->andReturn($prepared->image);

        // lqip
        $factory
            ->shouldReceive('image')
            ->with('source2.jpg')
            ->andReturn($prepared->image);

        $set = new ImageSet(
            [
                [
                    'image' => 'source1.jpg',
                    'widths' => [400, 800],
                    'media' => '(min-width: 1023px)',
                ],
                [
                    'image' => 'source2.jpg',
                    'widths' => [1200, 1600],
                    'media' => '(min-width: 1024px)',
                ],
            ],
            $factory
        );

        $data = $set->data();
        $this->assertEquals(2, count($data['sources']));

        $srcset = $data['sources'][0]['srcset'];
        $this->assertEquals(2, count($srcset));
        $item = $srcset[0];
        $this->assertEquals('cached1.jpg', $item['image']->cached()->url());
        $this->assertEquals(400, $item['width']);
        $item = $srcset[1];
        $this->assertEquals('cached2.jpg', $item['image']->cached()->url());
        $this->assertEquals(800, $item['width']);

        $srcset = $data['sources'][1]['srcset'];
        $this->assertEquals(2, count($srcset));
        $item = $srcset[0];
        $this->assertEquals('cached3.jpg', $item['image']->cached()->url());
        $this->assertEquals(1200, $item['width']);
        $item = $srcset[1];
        $this->assertEquals('cached4.jpg', $item['image']->cached()->url());
        $this->assertEquals(1600, $item['width']);

        $this->assertEquals('cached4.jpg', $data['lqip']->cached()->url());
    }

    public function test_can_render_imageset()
    {
        $prepared = $this->prepareImage();

        $prepared->cachedMock
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($prepared->image);

        $set = new ImageSet(
            [
                'image' => 'source.jpg',
                'widths' => [400, 800, 1200],
            ],
            $factory
        );

        $this->assertTrue($set->render() instanceof ImageSetRender);
    }

    public function test_can_configure_render_class()
    {
        $prepared = $this->prepareImage();

        $prepared->cachedMock
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($prepared->image);

        $set = new ImageSet(
            [
                'image' => 'source.jpg',
                'widths' => [400, 800, 1200],
            ],
            $factory
        );

        $set->setRenderClass(TestImageSetRender::class);

        $this->assertTrue($set->render() instanceof TestImageSetRender);
    }

    public function test_can_export_to_array()
    {
        $prepared = $this->prepareImage();

        $prepared->cachedMock
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        $prepared->cachedMock->shouldReceive('toArray')->andReturn(['_cached' => true]);

        $prepared->sourceMock->shouldReceive('toArray')->andReturn(['_source' => true]);

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($prepared->image);

        $set = new ImageSet(
            [
                'image' => 'source.jpg',
                'widths' => [400, 800, 1200],
            ],
            $factory
        );

        $imageData = [
            'source' => ['_source' => true],
            'cached' => ['_cached' => true],
        ];

        $this->assertEquals(
            [
                'sources' => [
                    [
                        'image' => 'source.jpg',
                        'widths' => [400, 800, 1200],
                        'srcset' => [
                            ['image' => $imageData, 'width' => 400],
                            ['image' => $imageData, 'width' => 800],
                            ['image' => $imageData, 'width' => 1200],
                        ],
                    ],
                ],
                'lqip' => $imageData,
            ],
            $set->toArray()
        );
    }

    public function test_can_prepare_sources_with_single_image_legacy_configuration()
    {
        $prepared = $this->prepareImage();

        $prepared->cachedMock
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        ($factory = $this->mockFactory())->shouldReceive('image')->andReturn($prepared->image);

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

        $this->assertEquals(1, count($data['sources']));

        $srcset = $data['sources'][0]['srcset'];

        $source = $srcset[0];
        $this->assertEquals('cached1.jpg', $source['image']->cached()->url());
        $this->assertEquals(400, $source['width']);

        $source = $srcset[1];
        $this->assertEquals('cached2.jpg', $source['image']->cached()->url());
        $this->assertEquals(800, $source['width']);

        $source = $srcset[2];
        $this->assertEquals('cached3.jpg', $source['image']->cached()->url());
        $this->assertEquals(1200, $source['width']);

        $this->assertEquals('cached3.jpg', $data['lqip']->cached()->url());
    }

    public function test_can_prepare_sources_with_multiple_images_legacy_configuration()
    {
        $prepared = $this->prepareImage();

        $prepared->cachedMock
            ->shouldReceive('url')
            ->andReturn('cached1.jpg', 'cached2.jpg', 'cached3.jpg');

        ($factory = $this->mockFactory())
            ->shouldReceive('image')
            ->with('source1.jpg', ['w' => 400])
            ->andReturn($prepared->image)
            ->shouldReceive('image')
            ->with('source2.jpg', ['w' => 800])
            ->andReturn($prepared->image)
            ->shouldReceive('image')
            ->with('source3.jpg', ['w' => 1200])
            ->andReturn($prepared->image);

        // lqip
        $factory
            ->shouldReceive('image')
            ->with('source3.jpg')
            ->andReturn($prepared->image);

        $set = new ImageSet(
            [
                'sources' => [
                    'sm' => [
                        'image' => 'source1.jpg',
                        'width' => 400,
                        'media' => '',
                    ],
                    'md' => [
                        'image' => 'source2.jpg',
                        'width' => 800,
                        'media' => '',
                    ],
                    'lg' => [
                        'image' => 'source3.jpg',
                        'width' => 1200,
                        'media' => '',
                    ],
                ],
            ],
            $factory
        );

        $data = $set->data();

        $this->assertEquals(3, count($data['sources']));

        $srcset = $data['sources'][0]['srcset'][0];
        $this->assertEquals('cached1.jpg', $srcset['image']->cached()->url());
        $this->assertEquals(400, $srcset['width']);

        $srcset = $data['sources'][1]['srcset'][0];
        $this->assertEquals('cached2.jpg', $srcset['image']->cached()->url());
        $this->assertEquals(800, $srcset['width']);

        $srcset = $data['sources'][2]['srcset'][0];
        $this->assertEquals('cached3.jpg', $srcset['image']->cached()->url());
        $this->assertEquals(1200, $srcset['width']);

        $this->assertEquals('cached3.jpg', $data['lqip']->cached()->url());
    }
}
