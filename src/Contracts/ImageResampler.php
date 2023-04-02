<?php

namespace Pboivin\Flou\Contracts;

use Pboivin\Flou\ResampledImage;

interface ImageResampler
{
    public const DEFAULT_RESAMPLING_DIR = '_r';

    public function resample(string $sourceFileName, array $glideParams): ResampledImage;
}
