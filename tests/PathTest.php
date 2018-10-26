<?php

use PHPUnit\Framework\TestCase;
use Flou\Path;
use Flou\Exception\InvalidFile;
use Flou\Exception\InvalidDirectory;

class PathTest extends TestCase
{
    public static $base_path;

    public static function setUpBeforeClass()
    {
        $current_dir = dirname(__FILE__);
        self::$base_path = Path::join($current_dir, "fixtures");
    }

    public function testValidateDirectory()
    {
        // Must exist
        $caught = false;
        try {
            $path = Path::join(self::$base_path, "notfound.jpg");
            Path::validateDirectory($path);
        } catch (InvalidDirectory $e) {
            $caught = true;
            $message = $e->getMessage();
            $this->assertRegExp("/path does not exist/", $message);
        }
        $this->assertTrue($caught);

        // Must be a directory
        $caught = false;
        try {
            $path = Path::join(self::$base_path, "image1.jpg");
            Path::validateDirectory($path);
        } catch (InvalidDirectory $e) {
            $caught = true;
            $message = $e->getMessage();
            $this->assertRegExp("/path is not a directory/", $message);
        }
        $this->assertTrue($caught);
    }

    public function testValidateFile()
    {
        // Must exist
        $caught = false;
        try {
            $path = Path::join(self::$base_path, "notfound.jpg");
            Path::validateFile($path);
        } catch (InvalidFile $e) {
            $caught = true;
            $message = $e->getMessage();
            $this->assertRegExp("/path does not exist/", $message);
        }
        $this->assertTrue($caught);

        // Must be a file
        $caught = false;
        try {
            $path = self::$base_path;
            Path::validateFile($path);
        } catch (InvalidFile $e) {
            $caught = true;
            $message = $e->getMessage();
            $this->assertRegExp("/path is not a file/", $message);
        }
        $this->assertTrue($caught);
    }

    public function testFilename()
    {
        $filename = Path::filename("hello.php");
        $this->assertEquals("hello", $filename);

        $filename = Path::filename("noextension");
        $this->assertEquals("noextension", $filename);

        $filename = Path::filename(".hidden.php");
        $this->assertEquals(".hidden", $filename);

        $filename = Path::filename(".hidden_no_extension");
        $this->assertEquals("", $filename);
    }

    public function testExtension()
    {
        $extension = Path::extension("hello.php");
        $this->assertEquals("php", $extension);

        $extension = Path::extension("noextension");
        $this->assertEquals("", $extension);

        $extension = Path::extension(".hidden.php");
        $this->assertEquals("php", $extension);

        $extension = Path::extension(".hidden_no_extension");
        $this->assertEquals("hidden_no_extension", $extension);
    }
}
