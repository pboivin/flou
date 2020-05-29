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
    private $image;
    protected $container_class = "flou-container";
    protected $img_class = "flou-image";
    protected $original_attr = "data-original";


    /**
     * constructor
     *
     * @param Flou\Image $image
     */
    public function __construct(Image $image=null)
    {
        if ($image) {
            $this->setImage($image);
        }
    }

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
     * Gets $image.
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
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
     * Sets the attribute name to contain the original image src.
     *
     * @param string $original_attr
     * @return DefaultImageRenderer $this
     */
    public function setOriginalAttr($original_attr)
    {
        $this->original_attr = $original_attr;
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
        $width = $this->image->getOriginalWidth();
        $height = $this->image->getOriginalHeight();
        $processed_url = $this->image->getProcessedURL();
        $original_url = $this->image->getOriginalURL();
        $alt = $this->image->getDescription();

        if ($original_url && $processed_url) {
            return <<<EOT
                <div class="{$this->container_class}">
                    <img
                        class="{$this->img_class}"
                        width="{$width}"
                        height="{$height}"
                        src="{$processed_url}"
                        {$this->original_attr}="{$original_url}"
                        alt="{$alt}"
                    />
                </div>
EOT;
        }
        return null;
    }
}
