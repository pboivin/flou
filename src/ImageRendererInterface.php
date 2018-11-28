<?php
namespace Flou;

use Flou\Image;

interface ImageRendererInterface
{
    public function setImage(Image $image);

    public function getImage();

    public function render();
}
