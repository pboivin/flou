<?php
namespace Flou;

use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface as ImagineImageInterface;
use Imagine\Gd\Imagine as DefaultImagine;
use Imagine\Image\Box;

use Flou\Exception\ImageProcessorException;
use Flou\ImageProcessorInterface;
use Flou\Image;

/**
 * Flou\DefaultImageProcessor is used by default to process a Flou\Image.
 */
class DefaultImageProcessor implements ImageProcessorInterface
{
    protected $image;
    protected $imagine;
    protected $imagine_image;
    protected $original_width;
    protected $original_height;
    protected $resize_width = 40;
    protected $blur_sigma = 0.5;


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
     * Get the Imagine instance to be used for processing. A DefaultImagine
     * (alised above) is instantiated by default if not configured.
     *
     * @return ImagineInterface
     */
    public function getImagine()
    {
        if (!$this->imagine) {
            $this->imagine = new DefaultImagine();
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
     * @throws ImageProcessorException If the image can't be processed.
     */
    public function process($save=true)
    {
        $input_file = $this->image->getOriginalFilePath();
        $imagine_image = $this->getImagineImage();
        $width = $this->original_width;
        $height = $this->original_height;
        $resize_width = $this->resize_width;
        $resize_height = $resize_width * $height / $width;

        try {
            $imagine_image->resize(new Box($resize_width, $resize_height));
            $imagine_image->effects()->blur($this->blur_sigma);
        } catch (\Exception $e) {
            throw new ImageProcessorException("Process failed for input file: $input_file");
        }

        if ($save) {
            $this->save();
        }
    }

    /**
     * Saves the generated image to a file.
     *
     * @throws ImageProcessorException If the image can't be saved.
     */
    public function save()
    {
        $input_file = $this->image->getOriginalFilePath();
        $output_file = $this->image->getProcessedFilePath();
        $imagine_image = $this->getImagineImage();

        try {
            $imagine_image->save($output_file);
        } catch (\Exception $e) {
            throw new ImageProcessorException("Save failed for input file: $input_file");
        }
    }
}
