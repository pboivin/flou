<?php

namespace Pboivin\Flou;

use Pboivin\Flou\Contracts\ImageMaker;

class ImageSetSources
{
    protected $imageForLqip = '';

    protected $preparedSources = [];

    public function __construct(
        protected array $config,
        protected ImageMaker $factory,
        protected ?array $glideParams = null
    ) {
        foreach ($config as $source) {
            $this->preparedSources[] = $this->prepareSource($source);

            $this->imageForLqip = $source['image'];
        }
    }

    protected function prepareSource(array $source): array
    {
        $srcset = [];
        $glideParams = $this->glideParams ?: [];

        if (isset($source['format'])) {
            $glideParams['fm'] = $source['format'];
        }

        foreach ($source['widths'] as $w) {
            $glideParams['w'] = $w;

            $srcset[] = [
                'image' => $this->factory->image($source['image'], $glideParams),
                'width' => $w,
            ];
        }

        $source['srcset'] = $srcset;

        return $source;
    }

    public function get(): array
    {
        return $this->preparedSources;
    }

    public function lqip(): Image
    {
        return $this->factory->image($this->imageForLqip);
    }

    public function toArray(): array
    {
        $data = $this->get();

        foreach ($data as &$source) {
            foreach ($source['srcset'] as &$item) {
                $item['image'] = $item['image']->toArray();
            }
        }

        return $data;
    }
}
