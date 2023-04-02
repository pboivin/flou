<?php

namespace Pboivin\Flou\Contracts;

use Pboivin\Flou\Image;

interface ImageMaker
{
    public const DEFAULT_GLIDE_PARAMS = ['h' => 10, 'fm' => 'gif'];

    public function image(string $source, ?array $glideParams = null): Image;
}
