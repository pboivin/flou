<?php

namespace Pboivin\Flou;

class Image
{
    public function __construct(protected ImageFile $source, protected ImageFile $cached)
    {
    }

    public function source(): ImageFile
    {
        return $this->source;
    }

    public function cached(): ImageFile
    {
        return $this->cached;
    }

    public function render(): ImageRender
    {
        return new ImageRender($this);
    }

    public function toArray(): array
    {
        return [
            'source' => $this->source()->toArray(),
            'cached' => $this->cached()->toArray(),
        ];
    }
}
