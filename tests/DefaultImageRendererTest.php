<?php

use PHPUnit\Framework\TestCase;
use Flou\DefaultImageRenderer;
use Flou\Image;
use Flou\Path;

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
            ->load("image1.jpg");

        $renderer = (new DefaultImageRenderer())
            ->setImage($image)
            ->setDescription("This is my image");

        // Cannot generate HTML without a base_url
        $html = $renderer->render();
        $this->assertNull($html);

        $image->setBaseUrl("/img");
        $html = $renderer->render();
        $this->assertNotNull($html);

        $expected_src = "/img/image1.flou.jpg";
        $expected_data_original = "/img/image1.jpg";

        // The default HTML is as expected
        $container = new SimpleXMLElement($html);
        $this->assertEquals("flou-container", $container['class']);
        $this->assertEquals(1, count($container->img));
        $this->assertEquals("flou-image", $container->img['class']);
        $this->assertEquals($expected_src, $container->img['src']);
        $this->assertEquals($expected_data_original, $container->img['data-original']);
        $this->assertEquals("This is my image", $container->img['alt']);

        // Test using custom CSS classes
        $renderer
            ->setContainerClass("my-container")
            ->setImgClass("my-img");
        $html = $renderer->render();
        $container = new SimpleXMLElement($html);
        $this->assertEquals("my-container", $container['class']);
        $this->assertEquals("my-img", $container->img['class']);
    }
}
