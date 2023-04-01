<?php

namespace Pboivin\Flou\Contracts;

use Pboivin\Flou\ImageSet;

interface ImageSetFactory
{
    public function imageSet(array $config, ?array $glideParams = null): ImageSet;
}
