<?php
namespace Flou;

use Flou\Exception\ImageProcessorException;
use Flou\ImageProcessorInterface;
use Flou\Image;

/**
 * Flou\DefaultImageProcessor is used by default to process an Image.
 */
class DefaultImageProcessor implements ImageProcessorInterface
{
    private $image;
    private $imagick_image;
    private $original_width;
    private $original_height;
    private $resize_width = 40;
    private $blur_radius = 10;
    private $blur_sigma = 10;


    /**
     * Sets the Image instance to be processed. Also inspects and saves the
     * original image's dimensions.
     *
     * @param Image $image
     * @return $this The Flou\DefaultImageProcessor instance.
     */
    public function setImage(Image $image)
    {
        $this->image = $image;

        $input_file = $image->getOriginalFilePath();
        $this->imagick_image = new \Imagick($input_file);

        $geometry = $this->imagick_image->getImageGeometry();
        $this->original_width = $geometry["width"];
        $this->original_height = $geometry["height"];

        return $this;
    }

    /**
     * Sets $blur_radius.
     *
     * @param string $value
     * @return $this The Flou\DefaultImageProcessor instance.
     */
    public function setBlurRadius($value)
    {
        $this->blur_radius = $value;
        return $this;
    }

    /**
     * Sets $blur_sigma.
     *
     * @param string $value
     * @return $this The Flou\DefaultImageProcessor instance.
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
     * @return $this The Flou\DefaultImageProcessor instance.
     */
    public function setResizeWidth($value)
    {
        $this->resize_width = $value;
        return $this;
    }

    /**
     *
     * @return int|null
     */
    public function getOriginalWidth()
    {
        return $this->original_width;
    }

    /**
     *
     * @return int|null
     */
    public function getOriginalHeight()
    {
        return $this->original_height;
    }

    /**
     * Generates a resized and blurred version of an original image then writes
     * the generated image to a file.
     *
     * @throws ImageProcessorException If the image can't be processed.
     */
    public function process()
    {
        $input_file = $this->image->getOriginalFilePath();
        $output_file = $this->image->getProcessedFilePath();

        $width = $this->original_width;
        $height = $this->original_height;
        $resize_width = $this->resize_width;
        $resize_height = $resize_width * $height / $width;
        $resized = $this->imagick_image->adaptiveResizeImage($resize_width, $resize_height, true);
        if (!$resized) {
            throw new ImageProcessorException("Resize failed: $input_file");
        }

        $radius = $this->blur_radius;
        $sigma = $this->blur_sigma;
        $blurred = $this->imagick_image->adaptiveBlurImage($radius, $sigma);
        if (!$blurred) {
            throw new ImageProcessorException("Blur failed: $input_file");
        }

        $written = $this->imagick_image->writeImage($output_file);
        if (!$written) {
            throw new ImageProcessorException("Write failed: $input_file");
        }
    }
}
