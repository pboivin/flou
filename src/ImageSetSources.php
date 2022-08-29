<?php

namespace Pboivin\Flou;

class ImageSetSources
{
    protected $imageForLqip = '';

    protected $preparedSources = [];

    public function __construct(protected array $config, protected ImageFactory $factory)
    {
        foreach ($config as $source) {
            $this->preparedSources[] = $this->prepareSource($source);

            $this->imageForLqip = $source['image'];
        }
    }

    protected function prepareSource(array $source): array
    {
        $srcset = [];

        foreach ($source['widths'] as $w) {
            $srcset[] = [
                'image' => $this->factory->image($source['image'], ['w' => $w]),
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
