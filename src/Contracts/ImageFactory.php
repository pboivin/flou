<?php

namespace Pboivin\Flou\Contracts;

use Pboivin\Flou\Image;

interface ImageFactory
{
    public function image(string $source, ?array $glideParams = null): Image;
}
