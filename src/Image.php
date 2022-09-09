<?php

namespace Pboivin\Flou;

class Image
{
    protected $renderClass = ImageRender::class;

    protected $renderOptions = [];

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

    public function setRenderClass(string $cls): void
    {
        $this->renderClass = $cls;
    }

    public function setRenderOptions(array $options): void
    {
        $this->renderOptions = $options;
    }

    public function render(): ImageRender
    {
        return new $this->renderClass($this, $this->renderOptions);
    }

    public function toArray(): array
    {
        return [
            'source' => $this->source()->toArray(),
            'cached' => $this->cached()->toArray(),
        ];
    }
}
