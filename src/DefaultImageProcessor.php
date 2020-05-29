<?php

namespace Flou;

use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface as ImagineImageInterface;
use Imagine\Image\Box;

use Flou\ImageProcessorInterface;
use Flou\Image;

/**
 * Flou\DefaultImageProcessor is used by default to process a Flou\Image.
 */
class DefaultImageProcessor implements ImageProcessorInterface
{
    private $image;
    private $imagine;
    private $imagine_image;
    private $original_width;
    private $original_height;
    protected $resize_width = 40;
    protected $blur_sigma = 0.5;
    protected $default_imagine_class = "Imagine\Gd\Imagine";

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
     * Sets the Imagine instance to be used for processing.
     *
     * @param ImagineInterface $imagine
     * @return DefaultImageProcessor $this
     */
    public function setImagine(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
        return $this;
    }

    /**
     * Get the Imagine instance to be used for processing. $default_imagine_class
     * is instantiated if not set.
     *
     * @return ImagineInterface
     */
    public function getImagine()
    {
        if (!$this->imagine) {
            $this->imagine = new $this->default_imagine_class;
        }
        return $this->imagine;
    }

    /**
     * Get the Imagine image ready to be processed.
     *
     * @return ImagineImageInterface
     */
    public function getImagineImage()
    {
        if (!$this->image) {
            return null;
        }
        if (!$this->imagine_image) {
            $original_file = $this->image->getOriginalFilePath();
            $imagine = $this->getImagine();
            $this->imagine_image = $imagine->open($original_file);
        }
        return $this->imagine_image;
    }

    /**
     * Sets the Flou\Image instance to be processed. Also inspects and saves the
     * original image's dimensions.
     *
     * @param Flou\Image $image
     * @return DefaultImageProcessor $this
     */
    public function setImage(Image $image)
    {
        $this->image = $image;
        $imagine_image = $this->getImagineImage();
        $size = $imagine_image->getSize();
        $this->original_width = $size->getWidth();
        $this->original_height = $size->getHeight();

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
     * Sets $blur_sigma.
     *
     * @param string $value
     * @return DefaultImageProcessor $this
     */
    public function setBlurSigma($value)
    {
        $this->blur_sigma = $value;
        return $this;
    }

    /**
     * Sets $resize_width.
     *
     * @param string $value
     * @return DefaultImageProcessor $this
     */
    public function setResizeWidth($value)
    {
        $this->resize_width = $value;
        return $this;
    }

    /**
     * Gets $original_width.
     *
     * @return int|null
     */
    public function getOriginalWidth()
    {
        return $this->original_width;
    }

    /**
     * Gets $original_height.
     *
     * @return int|null
     */
    public function getOriginalHeight()
    {
        return $this->original_height;
    }

    /**
     * Generates a resized and blurred version of an original image. Optionnaly
     * saves the generated image to a file.
     *
     * @param bool $save
     */
    public function process($save=true)
    {
        $this->resize();
        $this->blur();

        if ($save) {
            $this->save();
        }
    }

    /**
     * Resizes the image.
     */
    protected function resize()
    {
        $width = $this->original_width;
        $height = $this->original_height;
        $resize_width = $this->resize_width;
        $resize_height = $resize_width * $height / $width;
        $imagine_image = $this->getImagineImage();
        $imagine_image->resize(new Box($resize_width, $resize_height));
    }

    /**
     * Blurs the image.
     */
    protected function blur()
    {
        $imagine_image = $this->getImagineImage();
        $imagine_image->effects()->blur($this->blur_sigma);
    }

    /**
     * Saves the generated image to a file.
     */
    public function save()
    {
        $output_file = $this->image->getProcessedFilePath();
        $imagine_image = $this->getImagineImage();
        $imagine_image->save($output_file);
    }
}
