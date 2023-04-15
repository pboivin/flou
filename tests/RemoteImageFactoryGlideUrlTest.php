<?php

namespace Pboivin\Flou\Tests;

use InvalidArgumentException;
use Pboivin\Flou\Image;
use Pboivin\Flou\ImageRender;
use Pboivin\Flou\ImageSet;
use Pboivin\Flou\ImageSetRender;
use Pboivin\Flou\RemoteImageFactory;
use PHPUnit\Framework\TestCase;

class RemoteImageFactoryGlideUrlTest extends TestCase
{
    public function test_rejects_invalid_options()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid option 'test'.");

        $flou = new RemoteImageFactory(['test' => 'test']);
    }

    /**
     * @dataProvider requiredOptions
     */
    public function test_throws_exception_for_missing_options($optionName)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'$optionName' is not set.");

        $options = [
            'sourceUrlBase' => '/images/source',
            'glideUrlBase' => '/glide',
        ];

        unset($options[$optionName]);

        $flou = new RemoteImageFactory();

        $flou->$optionName();
    }

    public function requiredOptions()
    {
        return [['glideUrlBase']];
    }

    public function test_accepts_valid_options()
    {
        $flou = new RemoteImageFactory([
            'sourceUrlBase' => '/images/source',
            'glideUrlBase' => '/glide',
            'glideParams' => ['h' => 123],
            'renderOptions' => ['baseClass' => 'test'],
        ]);

        $this->assertEquals('/images/source', $flou->sourceUrlBase());
        $this->assertEquals('/glide', $flou->glideUrlBase());
        $this->assertEquals(['h' => 123], $flou->glideParams());
    }

    public function test_has_default_glide_params()
    {
        $flou = new RemoteImageFactory();

        $this->assertNotNull($flou->glideParams());
    }

    public function test_static_constructor()
    {
        $flou = RemoteImageFactory::create([
            'sourceUrlBase' => '/images/source',
            'glideUrlBase' => '/glide',
        ]);

        $this->assertEquals('/images/source', $flou->sourceUrlBase());
        $this->assertEquals('/glide', $flou->glideUrlBase());
    }

    public function test_generates_image_with_default_params()
    {
        $factory = new RemoteImageFactory(['glideUrlBase' => '/glide']);

        $image = $factory->image('source.jpg');

        $this->assertTrue($image instanceof Image);
        $this->assertEquals('/glide/source.jpg', $image->source()->url());
        $this->assertEquals('/glide/source.jpg?h=10&fm=gif', $image->cached()->url());

        $this->assertTrue($image->render() instanceof ImageRender);
    }

    public function test_generates_image_with_inline_glide_params()
    {
        $factory = new RemoteImageFactory(['glideUrlBase' => '/glide']);

        $image = $factory->image('source.jpg', ['h' => 123]);

        $this->assertTrue($image instanceof Image);
        $this->assertEquals('/glide/source.jpg', $image->source()->url());
        $this->assertEquals('/glide/source.jpg?h=123', $image->cached()->url());
    }

    public function test_generates_imageset()
    {
        $factory = new RemoteImageFactory(['glideUrlBase' => '/glide']);

        $set = $factory->imageSet([
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

    public function test_generates_imageset_with_custom_glide_params()
    {
        $factory = new RemoteImageFactory(['glideUrlBase' => '/glide']);

        $set = $factory->imageSet(
            [
                'image' => 'source.jpg',
                'widths' => [400, 800],
            ],
            [
                'filt' => 'greyscale',
            ]
        );

        $this->assertTrue($set instanceof ImageSet);

        $this->assertTrue($set->render() instanceof ImageSetRender);
    }
}
