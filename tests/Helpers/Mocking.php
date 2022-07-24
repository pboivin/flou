<?php

namespace Pboivin\Flou\Tests\Helpers;

use League\Glide\Server;
use Mockery;
use Pboivin\Flou\Image;
use Pboivin\Flou\ImageFactory;
use Pboivin\Flou\ImageFile;
use Pboivin\Flou\ImageFileInspector;
use Pboivin\Flou\ImageSet;

trait Mocking
{
    public function getFactory($options = [])
    {
        $factory = new ImageFactory(
            $options ?: [
                'sourcePath' => '/path/to/image/source',
                'cachePath' => '/path/to/image/cache',
                'sourceUrlBase' => '/images/source',
                'cacheUrlBase' => '/images/cache',
            ]
        );

        $factory->setGlideServer($this->mockServer());
        $factory->setInspector($this->mockInspector());

        return $factory;
    }

    public function getImage()
    {
        return new Image($this->mockImageFile(), $this->mockImageFile());
    }

    public function mockServer()
    {
        /** @var Server */
        $server = Mockery::mock(Server::class);

        return $server;
    }

    public function mockInspector()
    {
        /** @var ImageFileInspector */
        $inspector = Mockery::mock(ImageFileInspector::class);

        return $inspector;
    }

    public function mockImageFile()
    {
        /** @var ImageFile */
        $file = Mockery::mock(ImageFile::class);

        return $file;
    }

    public function mockFactory()
    {
        /** @var ImageFactory */
        $factory = Mockery::mock(ImageFactory::class);

        return $factory;
    }

    public function mockImage()
    {
        /** @var Image */
        $image = Mockery::mock(Image::class);

        return $image;
    }

    public function mockImageSet()
    {
        /** @var ImageSet */
        $set = Mockery::mock(ImageSet::class);

        return $set;
    }
}
