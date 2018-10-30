<?php
namespace Flou;

interface ImageRendererInterface
{
    public function setImage(Image $image);

    public function setDescription($description);

    public function render();
}
