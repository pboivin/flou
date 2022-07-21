<?php

namespace Tests\Unit;

use League\Glide\Server;
use Mockery;
use PHPUnit\Framework\TestCase;
use Pboivin\Flou\ImageFactory;

class ImageFactoryTest extends TestCase
{
    public function test_rejects_invalid_options()
    {
        $this->expectException(\InvalidArgumentException::class);

        $flou = new ImageFactory([
            'test' => 'test',
        ]);
    }

    public function test_accepts_valid_options()
    {
        $flou = new ImageFactory([
            'sourcePath' => '/path/to/image/source',
            'cachePath' => '/path/to/image/cache',
            'sourceUrlBase' => '/images/source',
            'cacheUrlBase' => '/images/cache',
            'glideParams' => ['h' => 123],
        ]);

        $this->assertEquals('/path/to/image/source', $flou->sourcePath());
        $this->assertEquals('/path/to/image/cache', $flou->cachePath());
        $this->assertEquals('/images/source', $flou->sourceUrlBase());
        $this->assertEquals('/images/cache', $flou->cacheUrlBase());
        $this->assertEquals(['h' => 123], $flou->glideParams());
    }

    public function test_has_default_options()
    {
        $flou = new ImageFactory();

        $this->assertNotNull($flou->glideParams());
    }

    public function test_generates_image_with_default_params()
    {
        $flou = new ImageFactory([
            'sourcePath' => '/path/to/image/source',
            'cachePath' => '/path/to/image/cache',
            'sourceUrlBase' => '/images/source',
            'cacheUrlBase' => '/images/cache',
        ]);

        $flou->setGlideServer($server = $this->getServer());

        $server->shouldReceive('makeImage')
            ->with('source.jpg', ImageFactory::DEFAULT_GLIDE_PARAMS)
            ->andReturn('cached.jpg');

        $image = $flou->image('source.jpg');

        $this->assertEquals('/images/cache/cached.jpg', $image->url());
        $this->assertEquals('/images/source/source.jpg', $image->sourceUrl());
    }

    public function test_generates_image_with_inline_params()
    {
        $flou = new ImageFactory([
            'sourcePath' => '/path/to/image/source',
            'cachePath' => '/path/to/image/cache',
            'sourceUrlBase' => '/images/source',
            'cacheUrlBase' => '/images/cache',
        ]);

        $flou->setGlideServer($server = $this->getServer());

        $server->shouldReceive('makeImage')
            ->with('source.jpg', ['h' => 123])
            ->andReturn('cached.jpg');

        $image = $flou->image('source.jpg', ['h' => 123]);

        $this->assertEquals('/images/cache/cached.jpg', $image->url());
        $this->assertEquals('/images/source/source.jpg', $image->sourceUrl());
    }

    public function getServer()
    {
        /** @var Server */
        $server = Mockery::mock(Server::class);

        return $server;
    }
}
