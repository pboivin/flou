<?php

use Imagine\Image\Box;

use Flou\DefaultImageProcessor;

class CustomImageProcessor extends DefaultImageProcessor
{
    public function process($save=true)
    {
        $imagine_image = $this->getImagineImage();
        $imagine_image->resize(new Box(1, 1));
        $this->save();
    }
}
