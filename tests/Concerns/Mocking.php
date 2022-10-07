<?php

namespace Pboivin\Flou\Tests\Concerns;

use League\Glide\Server;
use Mockery;
use Pboivin\Flou\Image;
use Pboivin\Flou\ImageFactory;
use Pboivin\Flou\ImageFile;
use Pboivin\Flou\ImageFileInspector;
use Pboivin\Flou\ImageSet;
use Pboivin\Flou\ResampledImage;

trait Mocking
{
    public function prepareFactory($options = []): object
    {
        $factory = new ImageFactory(
            $options ?: [
                'sourcePath' => '/path/to/image/source',
                'cachePath' => '/path/to/image/cache',
                'sourceUrlBase' => '/images/source',
                'cacheUrlBase' => '/images/cache',
            ]
        );

        $factory->setGlideServer($server = $this->mockServer());

        $factory->setInspector($inspector = $this->mockInspector());

        return (object) [
            'factory' => $factory,
            'serverMock' => $server,
            'inspectorMock' => $inspector,
        ];
    }

    public function prepareImage(): object
    {
        $source = $this->mockImageFile();

        $cached = $this->mockImageFile();

        $image = new Image($source, $cached);

        return (object) [
            'image' => $image,
            'sourceMock' => $source,
            'cachedMock' => $cached,
        ];
    }

    public function mockServer(): mixed
    {
        return Mockery::mock(Server::class);
    }

    public function mockInspector(): mixed
    {
        return Mockery::mock(ImageFileInspector::class);
    }

    public function mockImageFile(): mixed
    {
        return Mockery::mock(ImageFile::class);
    }

    public function mockFactory(): mixed
    {
        return Mockery::mock(ImageFactory::class);
    }

    public function mockImage(): mixed
    {
        return Mockery::mock(Image::class);
    }

    public function mockImageSet(): mixed
    {
        return Mockery::mock(ImageSet::class);
    }

    public function mockResampledImage(): mixed
    {
        return Mockery::mock(ResampledImage::class);
    }
}
