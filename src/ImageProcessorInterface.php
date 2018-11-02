<?php
namespace Flou;

interface ImageProcessorInterface
{
    public function setImage(Image $image);

    public function getOriginalWidth();

    public function getOriginalHeight();

    public function process();
}
