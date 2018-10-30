<?php

use PHPUnit\Framework\TestCase;
use Flou\Path;
use Flou\Image;


class DefaultImageRendererTest extends TestCase
{
    public static $base_path;


    public static function setUpBeforeClass()
    {
        $current_dir = dirname(__FILE__);
        self::$base_path = Path::join($current_dir, "fixtures");
    }

    /**
     * Generate the HTML markup for an image using default output settings
     */
    public function testRender()
    {
        $image = (new Flou\Image())
            ->setBasePath(self::$base_path)
            ->load("image1.jpg")
            ->process();
        $this->assertTrue($image->isProcessed());

        // Cannot generate HTML without a base_url
        $html = $image->render("Image Description");
        $this->assertNull($html);

        $html = $image
            ->setBaseUrl("/img")
            ->render("Image Description");
        $expected_src = "/img/image1.flou.jpg";
        $expected_data_original = "/img/image1.jpg";

        // The HTML is as expected
        $container = new SimpleXMLElement($html);
        $this->assertEquals("flou-container", $container['class']);
        $this->assertEquals(1, count($container->img));
        $this->assertEquals("flou-image", $container->img['class']);
        $this->assertEquals($expected_src, $container->img['src']);
        $this->assertEquals($expected_data_original, $container->img['data-original']);
        $this->assertEquals("Image Description", $container->img['alt']);
    }

#   /**
#    * Generate the HTML markup for an image **using custom output settings**
#    */
#   public function testRenderCustomOutput()
#   {
#       $custom_processed_basepath = self::$custom_processed_basepath;
#       $custom_processed_path = self::$custom_processed_path3;

#       $image = (new Flou\Image())
#           ->setBasePath(self::$base_path)
#           ->load("image1.jpg")
#           ->setProcessedPath($custom_processed_basepath)
#           ->setProcessedFile("image1.custom.jpg")
#           ->process();
#       $this->assertTrue($image->isProcessed());
#       $this->assertTrue(file_exists($custom_processed_path));

#       // Cannot generate HTML without a processed_url when using a custom
#       // output path
#       $html = $image
#           ->setBaseUrl("/img")
#           ->render("Image Description");
#       $this->assertNull($html);

#       $html = $image
#           ->setProcessedUrl("/custom")
#           ->render("Image Description");
#       $expected_src = "/custom/image1.custom.jpg";
#       $expected_data_original = "/img/image1.jpg";

#       // The HTML is as expected
#       $container = new SimpleXMLElement($html);
#       $this->assertEquals("flou-container", $container['class']);
#       $this->assertEquals(1, count($container->img));
#       $this->assertEquals("flou-image", $container->img['class']);
#       $this->assertEquals($expected_src, $container->img['src']);
#       $this->assertEquals($expected_data_original, $container->img['data-original']);
#       $this->assertEquals("Image Description", $container->img['alt']);
#   }
}
