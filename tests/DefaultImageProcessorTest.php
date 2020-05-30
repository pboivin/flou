<?php

use PHPUnit\Framework\TestCase;
use Flou\DefaultImageProcessor;
use Flou\Image;
use Flou\Path;

class DefaultImageProcessorTest extends TestCase
{
    public static $base_path;
    public static $processed_path;


    public static function setUpBeforeClass()
    {
        $current_dir = dirname(__FILE__);
        self::$base_path = Path::join($current_dir, "fixtures/images");
        self::$processed_path = Path::join(self::$base_path, "defaultImageProcessorOutput.jpg");

        self::_cleanup();
    }

    public static function tearDownAfterClass()
    {
        self::_cleanup();
    }

    public static function _cleanup()
    {
        if (file_exists(self::$processed_path)) {
            unlink(self::$processed_path);
        }
    }

    /**
     * Set the Image and check the geometry.
     */
    public function testGetOrginalGeometry()
    {
        $image = (new Flou\Image())
            ->setBasePath(self::$base_path)
            ->load("image1.jpg");

        $processor = (new DefaultImageProcessor())
            ->setImage($image);

        $this->assertNotNull($processor->getOriginalWidth());
        $this->assertNotNull($processor->getOriginalHeight());
    }

    /**
     * Set the Image and process it.
     */
    public function testProcess()
    {
        $image = (new Flou\Image())
            ->setBasePath(self::$base_path)
            ->load("image1.jpg");

        // Does not throw any exception
        $caught = false;
        try {
            $processor = (new DefaultImageProcessor())
                ->setImage($image)
                ->process();
        } catch (\Exception $e) {
            $caught = true;
        }
        $this->assertFalse($caught);
    }
}
