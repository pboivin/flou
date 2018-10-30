<?php

use PHPUnit\Framework\TestCase;
use Flou\Path;
use Flou\Image;


class ImageTest extends TestCase
{
    public static $base_path;
    public static $processed_path;
    public static $custom_processed_path1;
    public static $custom_processed_basepath;
    public static $custom_processed_path2;
    public static $custom_processed_path3;


    public static function setUpBeforeClass()
    {
        $current_dir = dirname(__FILE__);
        self::$base_path = Path::join($current_dir, "fixtures");
        self::$processed_path = Path::join(self::$base_path, "image1.flou.jpg");
        self::$custom_processed_path1 = Path::join(self::$base_path, "processed_image.jpg");
        self::$custom_processed_basepath = Path::join($current_dir, "fixtures", "tmp");
        self::$custom_processed_path2 = Path::join(self::$custom_processed_basepath, "image1.flou.jpg");
        self::$custom_processed_path3 = Path::join(self::$custom_processed_basepath, "image1.custom.jpg");

        self::_cleanup();
        mkdir(self::$custom_processed_basepath);
    }

    public static function tearDownAfterClass()
    {
        self::_cleanup();
    }

    public static function _cleanup()
    {
        $files = [
            self::$processed_path,
            self::$custom_processed_path1,
            self::$custom_processed_path2,
            self::$custom_processed_path3,
        ];
        $dirs = [
            self::$custom_processed_basepath,
        ];
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        foreach ($dirs as $dir) {
            if (file_exists($dir)) {
                rmdir($dir);
            }
        }
    }

    /**
     * Call `setBasePath` and load an image relative to that path
     */
    public function testLoadWithBasePath()
    {
        $path = Path::join(self::$base_path, "image1.jpg");
        $image = (new Flou\Image())
            ->setBasePath(self::$base_path)
            ->load("image1.jpg");

        // `getOriginalFilePath` returns the full path to the original file
        $this->assertEquals($path, $image->getOriginalFilePath());
    }

    /**
     * Load an image using it's full path
     */
    public function testLoadFullPath()
    {
        $path = Path::join(self::$base_path, "image1.jpg");
        $image = (new Flou\Image())->load($path);

        // `getOriginalFilePath` returns the full path to the original file;
        // the base path was extracted from the full path provided to `load`
        $this->assertEquals($path, $image->getOriginalFilePath());
    }

    /**
     * Process an image using the default output path and default output filename
     */
    public function testProcess()
    {
        $processed_path = self::$processed_path;
        $this->assertFalse(file_exists($processed_path));

        $image = (new Flou\Image())
            ->setBasePath(self::$base_path)
            ->load("image1.jpg");

        // `getProcessedFilePath` returns the expected full path to the processed
        // image (not yet processed)
        $this->assertEquals($processed_path, $image->getProcessedFilePath());
        $this->assertFalse($image->isProcessed());

        // Process and save the image
        $image->process();
        $this->assertTrue($image->isProcessed());
        $this->assertTrue(file_exists($processed_path));
    }

    /**
     * Process an image using the default output path and **a custom output filename**
     */
    public function testCustomProcessedFile()
    {
        $initial_processed_path = self::$processed_path;
        $custom_processed_path = self::$custom_processed_path1;
        $this->assertFalse(file_exists($custom_processed_path));

        $image = (new Flou\Image())
            ->setBasePath(self::$base_path)
            ->load("image1.jpg");

        // `getProcessedFilePath` returns the expected default path
        $this->assertEquals($initial_processed_path, $image->getProcessedFilePath());

        $image->setProcessedFile("processed_image.jpg");
        $image->process();
        $this->assertTrue($image->isProcessed());

        // `getProcessedFilePath` return the custom path
        $this->assertEquals($custom_processed_path, $image->getProcessedFilePath());
        $this->assertTrue(file_exists($custom_processed_path));
    }

    /**
     * Process an image using **a custom output path** and the default output filename
     */
    public function testCustomProcessedPath()
    {
        $initial_processed_path = self::$processed_path;
        $custom_processed_basepath = self::$custom_processed_basepath;
        $custom_processed_path = self::$custom_processed_path2;
        $this->assertFalse(file_exists($custom_processed_path));

        $image = (new Flou\Image())
            ->setBasePath(self::$base_path)
            ->load("image1.jpg");
        $this->assertEquals($initial_processed_path, $image->getProcessedFilePath());

        $image->setProcessedPath($custom_processed_basepath);
        $image->process();
        $this->assertTrue($image->isProcessed());
        $this->assertEquals($custom_processed_path, $image->getProcessedFilePath());
        $this->assertTrue(file_exists($custom_processed_path));
    }

    /**
     * Force-process an image that has already been processed
     */
    public function testForceProcess()
    {
        $processed_path = self::$processed_path;

        $image = (new Flou\Image())
            ->setBasePath(self::$base_path)
            ->load("image1.jpg")
            ->process();
        $mtime1 = filemtime($processed_path);

        // The image was processed and saved
        $this->assertTrue($image->isProcessed());

        sleep(1);
        $image->process();
        $mtime2 = filemtime($processed_path);

        // The processed image already existed, it was not regenerated
        $this->assertEquals($mtime1, $mtime2);

        sleep(1);
        $image->forceProcess();
        $mtime3 = filemtime($processed_path);

        // The processed image already existed, but it was regenerated by forceProcess
        $this->assertNotEquals($mtime2, $mtime3);
    }

    /**
     * Generate the HTML markup for an image using default output settings
     */
    public function testRender()
    {
        $image = (new Flou\Image())
            ->setBasePath(self::$base_path)
            ->setBaseUrl("/img")
            ->load("image1.jpg")
            ->process();
        $this->assertTrue($image->isProcessed());

        // Cannot generate HTML without a base_url
        $html = $image->render("Image Description");
        $this->assertNotNull($html);
    }
}
