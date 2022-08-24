<?php

namespace Pboivin\Flou\Tests;

use InvalidArgumentException;
use Pboivin\Flou\ImageFactory;
use Pboivin\Flou\ImageRender;
use Pboivin\Flou\ImageSet;
use Pboivin\Flou\ImageSetRender;
use Pboivin\Flou\Tests\Concerns\Mocking;
use PHPUnit\Framework\TestCase;

class ImageFactoryTest extends TestCase
{
    use Mocking;

    public function test_rejects_invalid_options()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid option 'test'.");

        $flou = new ImageFactory(['test' => 'test']);
    }

    /**
     * @dataProvider requiredOptions
     */
    public function test_throws_exception_for_missing_options($optionName)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'$optionName' is not set.");

        $options = [
            'sourcePath' => '/path/to/image/source',
            'cachePath' => '/path/to/image/cache',
            'sourceUrlBase' => '/images/source',
            'cacheUrlBase' => '/images/cache',
        ];

        unset($options[$optionName]);

        $flou = new ImageFactory();

        $flou->$optionName();
    }

    public function requiredOptions()
    {
        return [['sourcePath'], ['cachePath'], ['sourceUrlBase'], ['cacheUrlBase']];
    }

    public function test_accepts_valid_options()
    {
        $flou = new ImageFactory([
            'sourcePath' => '/path/to/image/source',
            'cachePath' => '/path/to/image/cache',
            'sourceUrlBase' => '/images/source',
            'cacheUrlBase' => '/images/cache',
            'glideParams' => ['h' => 123],
            'renderOptions' => ['baseClass' => 'test'],
        ]);

        $this->assertEquals('/path/to/image/source', $flou->sourcePath());
        $this->assertEquals('/path/to/image/cache', $flou->cachePath());
        $this->assertEquals('/images/source', $flou->sourceUrlBase());
        $this->assertEquals('/images/cache', $flou->cacheUrlBase());
        $this->assertEquals(['h' => 123], $flou->glideParams());
    }

    public function test_has_default_glide_params()
    {
        $flou = new ImageFactory();

        $this->assertNotNull($flou->glideParams());
    }

    public function test_static_constructor()
    {
        $flou = ImageFactory::create([
            'sourcePath' => '/path/to/image/source',
            'cachePath' => '/path/to/image/cache',
            'sourceUrlBase' => '/images/source',
            'cacheUrlBase' => '/images/cache',
        ]);

        $this->assertEquals('/path/to/image/source', $flou->sourcePath());
        $this->assertEquals('/path/to/image/cache', $flou->cachePath());
        $this->assertEquals('/images/source', $flou->sourceUrlBase());
        $this->assertEquals('/images/cache', $flou->cacheUrlBase());
    }

    public function test_generates_image_with_default_params()
    {
        $flou = $this->getFactory();

        $flou
            ->glideServer()
            ->shouldReceive('makeImage')
            ->with('source.jpg', ImageFactory::DEFAULT_GLIDE_PARAMS)
            ->andReturn('cached.jpg');

        $image = $flou->image('source.jpg');

        $this->assertEquals('/images/cache/cached.jpg', $image->cached()->url());
        $this->assertEquals('/images/source/source.jpg', $image->source()->url());

        $this->assertTrue($image->render() instanceof ImageRender);
    }

    public function test_generates_image_with_inline_glide_params()
    {
        $flou = $this->getFactory();

        $flou
            ->glideServer()
            ->shouldReceive('makeImage')
            ->with('source.jpg', ['h' => 123])
            ->andReturn('cached.jpg');

        $image = $flou->image('source.jpg', ['h' => 123]);

        $this->assertEquals('/images/cache/cached.jpg', $image->cached()->url());
        $this->assertEquals('/images/source/source.jpg', $image->source()->url());
    }

    public function test_generates_imageset()
    {
        $flou = $this->getFactory();

        $flou
            ->glideServer()
            ->shouldReceive('makeImage')
            ->andReturn('cached.jpg');

        $set = $flou->imageSet([
            'image' => 'source.jpg',
            'sources' => [
                'sm' => ['width' => 400],
                'md' => ['width' => 800],
                'lg' => ['width' => 1200],
            ],
        ]);

        $this->assertTrue($set instanceof ImageSet);

        $this->assertTrue($set->render() instanceof ImageSetRender);
    }
}
