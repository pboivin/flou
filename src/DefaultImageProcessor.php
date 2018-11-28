<?php
namespace Flou;

use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface as ImagineImageInterface;
use Imagine\Image\Box;

use Flou\ConfigurableTrait;
use Flou\ImageProcessorInterface;
use Flou\Image;

/**
 * Flou\DefaultImageProcessor is used by default to process a Flou\Image.
 */
class DefaultImageProcessor implements ImageProcessorInterface
{
    use ConfigurableTrait;

    private $image;
    private $imagine;
    private $imagine_image;
    private $resize_width;
    private $blur_sigma;
    private $original_width;
    private $original_height;


    /**
     * Initialize the class default values.
     */
    public static function initialize()
    {
        self::configure([
            "resize_width" => 40,
            "blur_sigma" => 0.5,
            "imagine_class" => "Imagine\Gd\Imagine",
        ]);
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
     * Get the Imagine instance to be used for processing. A default imagine class
     * (configurable) is instantiated if not set.
     *
     * @return ImagineInterface
     */
    public function getImagine()
    {
        if (!$this->imagine) {
            $imagine_class = self::getConfig("imagine_class");
            $this->imagine = new $imagine_class;
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
     * Gets $blur_sigma.
     *
     * @return string
     */
    public function getBlurSigma()
    {
        return $this->blur_sigma ?? self::getConfig("blur_sigma");
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
     * Gets $resize_width.
     *
     * @return string
     */
    public function getResizeWidth()
    {
        return $this->resize_width ?? self::getConfig("resize_width");
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
        $resize_width = $this->getResizeWidth();
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

        $imagine_image->effects()->blur($this->getBlurSigma());
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

DefaultImageProcessor::initialize();
