<?php

namespace Pboivin\Flou\Concerns;

use Pboivin\Flou\Contracts\ImageResampler;
use Pboivin\Flou\ImageFactory;

trait HasResampler
{
    protected $resampler;

    public function resampler(): ImageFactory
    {
        if (!$this->resampler) {
            $this->resampler = new static([
                'sourcePath' => $this->cachePath(),
                'cachePath' => $this->cachePath() . '/' . ImageResampler::DEFAULT_RESAMPLING_DIR,
                'sourceUrlBase' => $this->cacheUrlBase(),
                'cacheUrlBase' => $this->cacheUrlBase() . '/' . ImageResampler::DEFAULT_RESAMPLING_DIR,
                'glideParams' => $this->glideParams(),
                'renderOptions' => $this->renderOptions ?: [],
            ]);
        }

        return $this->resampler;
    }

    public function setResampler(ImageFactory $resamplingFactory): void
    {
        $this->resampler = $resamplingFactory;
    }
}
