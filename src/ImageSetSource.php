<?php

namespace Pboivin\Flou;

use InvalidArgumentException;

class ImageSetSource
{
    protected $data;

    public function __construct(protected array $config, protected ImageSet $imageSet)
    {
        $this->acceptConfig($config);
    }

    protected function acceptConfig(array $config)
    {
        if (!isset($config['width'])) {
            throw new InvalidArgumentException("Source is missing required 'width' option.");
        }

        if (!isset($config['image']) && !$this->imageSet->image()) {
            throw new InvalidArgumentException(
                "Missing required 'image' option on source or imageset."
            );
        }

        $config['image'] ??= $this->imageSet->image();

        $this->data = $config;
    }

    public function data(): array
    {
        return [
            'image' => $this->imageSet->factory()->image($this->data['image'], [
                'w' => $this->data['width'],
            ]),
            'width' => "{$this->data['width']}",
        ];
    }
}
