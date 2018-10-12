<?php

require_once "Flou.php";
use Flou\Path;
use Flou\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase {
    public static $base_path;

    public static function setUpBeforeClass() {
        $current_dir = dirname(__FILE__);
        self::$base_path = Path::join($current_dir, "fixtures");
    }

    public function testLoad() {
        $image = (new Flou\Image())
            ->setBasePath(self::$base_path)
            ->load("image1.jpg");
        $this->assertNotNull($image->getOriginalFilePath());
    }
}
