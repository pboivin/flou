<?php

namespace Pboivin\Flou\Contracts;

use Pboivin\Flou\Image;

interface ImageMaker
{
    public function image(string $source, ?array $glideParams = null): Image;
}
