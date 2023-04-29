<?php

namespace Pboivin\Flou\Tests;

use League\Glide\ServerFactory;
use League\Glide\Urls\UrlBuilderFactory;
use Pboivin\Flou\Image;
use Pboivin\Flou\ImageRender;
use Pboivin\Flou\ImageSet;
use Pboivin\Flou\ImageSetRender;
use Pboivin\Flou\RemoteImageFactory;
use PHPUnit\Framework\TestCase;

class RemoteImageFactoryGlideServerTest extends TestCase
{
    private function createGlideServer()
    {
        return ServerFactory::create([
            'source' => '/tmp/images/source',
            'cache' => '/tmp/images/cache',
            'base_url' => '/glide',
        ]);
    }

    private function signUrl($path, $params)
    {
        return UrlBuilderFactory::create('/glide/', 'secret')
            ->getUrl($path, $params);
    }

    public function test_accepts_glide_server()
    {
        $factory = new RemoteImageFactory([
            'glideServer' => $this->createGlideServer(),
            'glideUrlSignKey' => 'secret',
        ]);

        $this->assertEquals('glide', $factory->sourceUrlBase());
        $this->assertEquals('glide', $factory->glideUrlBase());
    }

    public function test_generates_image_with_default_params()
    {
        $factory = new RemoteImageFactory([
            'glideServer' => $this->createGlideServer(),
            'glideUrlSignKey' => 'secret',
        ]);

        $image = $factory->image('source.jpg');

        $this->assertTrue($image instanceof Image);
        $this->assertEquals($this->signUrl('source.jpg', []), $image->source()->url());
        $this->assertEquals($this->signUrl('source.jpg', ['h'=>10, 'fm'=>'gif']), $image->cached()->url());

        $this->assertTrue($image->render() instanceof ImageRender);
    }

    public function test_generates_image_with_inline_glide_params()
    {
        $factory = new RemoteImageFactory([
            'glideServer' => $this->createGlideServer(),
            'glideUrlSignKey' => 'secret',
        ]);

        $image = $factory->image('source.jpg', ['h' => 123]);

        $this->assertTrue($image instanceof Image);
        $this->assertEquals($this->signUrl('source.jpg', []), $image->source()->url());
        $this->assertEquals($this->signUrl('source.jpg', ['h'=>123]), $image->cached()->url());
    }

    public function test_generates_imageset()
    {
        $factory = new RemoteImageFactory([
            'glideServer' => $this->createGlideServer(),
            'glideUrlSignKey' => 'secret',
        ]);

        $set = $factory->imageSet([
            'image' => 'source.jpg',
            'widths' => [400, 800],
        ]);

        $this->assertTrue($set instanceof ImageSet);

        $this->assertTrue($set->render() instanceof ImageSetRender);
    }

    public function test_generates_imageset_with_custom_glide_params()
    {
        $factory = new RemoteImageFactory([
            'glideServer' => $this->createGlideServer(),
            'glideUrlSignKey' => 'secret',
        ]);

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
