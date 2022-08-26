<?php

namespace Pboivin\Flou;

use InvalidArgumentException;

class ImageSetSources
{
    protected $imageForLqip = '';

    protected $data = [];

    public function __construct(protected array $config, protected ImageSet $imageSet)
    {
        $this->acceptConfig($config);
    }

    protected function acceptConfig(array $config)
    {
        foreach ($config as $source) {
            if (!isset($source['width'])) {
                throw new InvalidArgumentException("Source is missing required 'width' option.");
            }

            if (!isset($source['image']) && !$this->imageSet->image()) {
                throw new InvalidArgumentException(
                    "Missing required 'image' option on source or imageset."
                );
            }

            $source['image'] ??= $this->imageSet->image();

            $this->imageForLqip = $source['image'];

            $this->data[] = $source;
        }
    }

    public function all(): array
    {
        return array_map(
            fn ($i) => [
                'image' => $this->imageSet->factory()->image($i['image'], [
                    'w' => $i['width'],
                ]),
                'width' => "{$i['width']}",
            ],
            $this->data
        );
    }

    public function lqip(): Image
    {
        return $this->imageSet->factory()->image($this->imageForLqip);
    }
}
