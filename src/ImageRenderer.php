<?php
namespace Flou;

use Flou\Image;

/**
 * Flou\ImageRenderer ...
 */
class ImageRenderer
{
    private $image;
    private $container_class = "flou-container";
    private $img_class = "flou-image";
    private $alt_text = "";

    /**
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     */
    public function setContainerClass($container_class)
    {
        $this->container_class = $container_class;
        return $this;
    }

    /**
     */
    public function setImgClass($img_class)
    {
        $this->img_class = $img_class;
        return $this;
    }

    /**
     */
    public function setAltText($alt_text)
    {
        $this->alt_text = $alt_text;
        return $this;
    }

    /**
     * Returns the HTML code to display the an image on a web page.
     *
     * The URL of the original image is attached to the processed image via
     * the data-original attribute, which can be used to implement lazy-loading.
     *
     * @return string|null
     */
    public function render()
    {
        $container_class = $this->container_class;
        $img_class = $this->img_class;
        $width = $this->image->getOriginalWidth();
        $height = $this->image->getOriginalHeight();
        $processed_url = $this->image->getProcessedURL();
        $original_url = $this->image->getOriginalURL();
        $alt = $this->alt_text;

        $template = sprintf(
            '<div class="%s">' .
                '<img class="%s" width="%s" height="%s" src="%s" data-original="%s" alt="%s" />' .
            '</div>',
            $container_class, $img_class, $width, $height, $processed_url, $original_url, $alt
        );

        if ($original_url && $processed_url) {
            return $template;
        }
        return null;
    }
}
