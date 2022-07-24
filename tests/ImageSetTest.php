<?php

namespace Pboivin\Flou\Tests;

use InvalidArgumentException;
use Pboivin\Flou\ImageSet;
use PHPUnit\Framework\TestCase;
use Pboivin\Flou\Tests\Helpers\Mocking;

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

        $set->toArray();
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

        $set->toArray();
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

        $this->assertEquals(
            [
                'src' => 'cached3.jpg',
                'sizes' => '100vw',
                'srcset' => ['cached1.jpg 400w', 'cached2.jpg 800w', 'cached3.jpg 1200w'],
            ],
            $set->toArray()
        );
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

        $this->assertEquals(
            [
                'src' => 'cached3.jpg',
                'sizes' => '100vw',
                'srcset' => ['cached1.jpg 400w', 'cached2.jpg 800w', 'cached3.jpg 1200w'],
            ],
            $set->toArray()
        );
    }

    // TODO add render test
}
