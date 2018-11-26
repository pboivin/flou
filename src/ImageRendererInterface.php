<?php
namespace Flou;

use Flou\Image;

interface ImageRendererInterface
{
    public function setImage(Image $image);

    public function setDescription($description);

    public function render();
}
