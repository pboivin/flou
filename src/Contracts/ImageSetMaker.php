<?php

namespace Pboivin\Flou\Contracts;

use Pboivin\Flou\ImageSet;

interface ImageSetMaker
{
    public function imageSet(array $config, ?array $glideParams = null): ImageSet;
}
