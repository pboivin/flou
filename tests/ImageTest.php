<?php

use PHPUnit\Framework\TestCase;
use Flou\Path;
use Flou\Image;

class ImageTest extends TestCase {
    public static $base_path;

    public static function setUpBeforeClass() {
        $current_dir = dirname(__FILE__);
        self::$base_path = Path::join($current_dir, "fixtures");
    }

    public function testLoadWithBasePath() {
        $image = (new Flou\Image())
            ->setBasePath(self::$base_path)
            ->load("image1.jpg");
        $this->assertNotNull($image->getOriginalFilePath());
    }

    public function testLoadFullPath() {
        $path = Path::join(self::$base_path, "image1.jpg");
        $image = (new Flou\Image())->load($path);
        $this->assertEquals($path, $image->getOriginalFilePath());
    }

    public function testProcess() {
        $processed_path = self::$processed_path;
        $this->assertFalse(file_exists($processed_path));

        $image = (new Flou\Image())
            ->setBasePath(self::$base_path)
            ->load("image1.jpg");
        $this->assertEquals($processed_path, $image->getProcessedFilePath());
        $this->assertFalse($image->isProcessed());

        $image->process();
        $this->assertTrue($image->isProcessed());
        $this->assertTrue(file_exists($processed_path));

        unlink($processed_path);
    }
}
