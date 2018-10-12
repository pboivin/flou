<?php

require "Flou.php";
use Flou\Path;
use Flou\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase {
    public static $base_path;

    public static function setUpBeforeClass() {
        $current_dir = dirname(__FILE__);
        self::$base_path = Path::join($current_dir, "fixtures");
    }

    public function testSetBasePath() {
        // Must exist
        try {
            $image = new Flou\Image();
            $image->setBasePath(Path::join(self::$base_path, "notfound.jpg"));
        } catch(\Exception $e) {
            $message = $e->getMessage();
            $this->assertRegExp("/Base path does not exist/", $message);
        }

        // Must be a directory
        try {
            $image = new Flou\Image();
            $image->setBasePath(Path::join(self::$base_path, "image1.jpg"));
        } catch(\Exception $e) {
            $message = $e->getMessage();
            $this->assertRegExp("/Base path is not a directory/", $message);
        }
    }

    public function testLoad() {
        $image = new Flou\Image();
        $image->setBasePath(self::$base_path);
        $image->load("image1.jpg");
        $file_path = $image->getOriginalFilePath();
        $this->assertNotNull($file_path);
    }
}
