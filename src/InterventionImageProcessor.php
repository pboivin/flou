<?php

namespace Flou;

use Intervention\Image\ImageManager;

use Flou\ImageProcessorInterface;
use Flou\Image;

/**
 * Flou\InterventionImageProcessor uses Intervention to process a Flou\Image.
 */
class InterventionImageProcessor implements ImageProcessorInterface
{
    private $image;
    private $original_width;
    private $original_height;
    private $intervention_manager;
    private $intervention_image;
    protected $resize_width = 40;
    protected $blur_amount = 50;

    /**
     * constructor
     *
     * @param Flou\Image $image
     */
    public function __construct(Image $image=null)
    {
        $this->intervention_manager = new ImageManager(array('driver' => 'gd'));

        if ($image) {
            $this->setImage($image);
        }
    }

    /**
     * Sets the Flou\Image instance to be processed. Also inspects and saves the
     * original image's dimensions.
     *
     * @param Flou\Image $image
     * @return InterventionImageProcessor $this
     */
    public function setImage(Image $image)
    {
        $this->image = $image;
        $path = $this->image->getOriginalFilePath();
        $this->intervention_image = $this->intervention_manager->make($path);
        $this->original_width = $this->intervention_image->getWidth();
        $this->original_height = $this->intervention_image->getHeight();

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
        $this->intervention_image->widen($this->resize_width);
        $this->intervention_image->blur($this->blur_amount);

        if ($save) {
            $this->save();
        }
    }

    /**
     * Saves the generated image to a file.
     */
    public function save()
    {
        $output_file = $this->image->getProcessedFilePath();
        $this->intervention_image->save($output_file);
    }
}
