<?php

namespace Pboivin\Flou;

class Image
{
    protected $renderClass = ImageRender::class;

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

    public function setRenderClass(string $cls)
    {
        $this->renderClass = $cls;
    }

    public function render(): ImageRender
    {
        return new ($this->renderClass)($this);
    }

    public function toArray(): array
    {
        return [
            'source' => $this->source()->toArray(),
            'cached' => $this->cached()->toArray(),
        ];
    }
}
