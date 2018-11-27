<?php
namespace Flou;

use Flou\ImageRendererInterface;
use Flou\Image;

/**
 * Flou\DefaultImageRenderer is used by default to render a processed Flou\Image
 * instance to HTML.
 */
class DefaultImageRenderer implements ImageRendererInterface
{
    protected $image;
    protected $container_class = "flou-container";
    protected $img_class = "flou-image";


    /**
     * Sets the Image instance to be rendered.
     *
     * @param Image $image
     * @return DefaultImageRenderer $this
     */
    public function setImage(Image $image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Sets the CSS class to be used for the container element.
     *
     * @param string $container_class
     * @return DefaultImageRenderer $this
     */
    public function setContainerClass($container_class)
    {
        $this->container_class = $container_class;
        return $this;
    }

    /**
     * Sets the CSS class to be used for the img element.
     *
     * @param string $img_class
     * @return DefaultImageRenderer $this
     */
    public function setImgClass($img_class)
    {
        $this->img_class = $img_class;
        return $this;
    }

    /**
     * Returns the HTML code to display the processed image on a Web page.
     *
     * The URL of the original image is attached to the img tag via
     * the data-original attribute, which is used to implement lazy-loading.
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
        $alt = $this->image->getDescription();

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
