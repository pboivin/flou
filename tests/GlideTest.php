<?php

namespace Pboivin\Flou\Tests;

use PHPUnit\Framework\TestCase;
use Pboivin\Flou\ImageFactory;

class GlideTest extends TestCase
{
    protected $factory;
    protected $sourcePath;
    protected $cachePath;
    protected $cachedFile;

    protected function setUp(): void
    {
        $this->sourcePath = __DIR__ . '/Fixtures/source';

        $this->cachePath = __DIR__ . '/Fixtures/__cache__';

        $this->factory = new ImageFactory([
            'sourcePath' => $this->sourcePath,
            'cachePath' => $this->cachePath,
            'sourceUrlBase' => '/images/source',
            'cacheUrlBase' => '/images/cache',
        ]);

        $this->cleanCacheDirectories();
    }

    protected function cleanCacheDirectories()
    {
        if (file_exists($this->cachePath . '/square.jpg')) {
            foreach (glob($this->cachePath . '/square.jpg/*') as $file) {
                unlink($file);
            }

            rmdir($this->cachePath . '/square.jpg');
        }
    }

    public function test_can_transform_source_image()
    {
        $this->assertFalse(file_exists($this->cachePath . '/square.jpg'));

        $image = $this->factory->image('square.jpg');

        $this->assertTrue(file_exists($this->cachePath . '/square.jpg'));
        $this->assertTrue(file_exists($image->cached()->path()));
    }
}
