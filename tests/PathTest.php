<?php

use PHPUnit\Framework\TestCase;
use Flou\Path;
use Flou\Exception\InvalidFile;
use Flou\Exception\InvalidDirectory;

class PathTest extends TestCase {
    public static $base_path;

    public static function setUpBeforeClass() {
        $current_dir = dirname(__FILE__);
        self::$base_path = Path::join($current_dir, "fixtures");
    }

    public function testCheckDirectory() {
        // Must exist
        $caught = false;
        try {
            $path = Path::join(self::$base_path, "notfound.jpg");
            Path::checkDirectory($path);
        } catch(InvalidDirectory $e) {
            $caught = true;
            $message = $e->getMessage();
            $this->assertRegExp("/path does not exist/", $message);
        }
        $this->assertTrue($caught);

        // Must be a directory
        try {
        $caught = false;
            $path = Path::join(self::$base_path, "image1.jpg");
            Path::checkDirectory($path);
        } catch(InvalidDirectory $e) {
            $caught = true;
            $message = $e->getMessage();
            $this->assertRegExp("/path is not a directory/", $message);
        }
        $this->assertTrue($caught);
    }

    public function testCheckFile() {
        // Must exist
        $caught = false;
        try {
            $path = Path::join(self::$base_path, "notfound.jpg");
            Path::checkFile($path);
        } catch(InvalidFile $e) {
            $caught = true;
            $message = $e->getMessage();
            $this->assertRegExp("/path does not exist/", $message);
        }
        $this->assertTrue($caught);

        // Must be a file
        $caught = false;
        try {
            $path = self::$base_path;
            Path::checkFile($path);
        } catch(InvalidFile $e) {
            $caught = true;
            $message = $e->getMessage();
            $this->assertRegExp("/path is not a file/", $message);
        }
        $this->assertTrue($caught);
    }
}
