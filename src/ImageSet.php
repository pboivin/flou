<?php

namespace Pboivin\Flou;

use Pboivin\Flou\Contracts\ImageFactory as ImageContract;

class ImageSet
{
    public const DEFAULT_SIZES_VALUE = '100vw';

    protected $renderClass = ImageSetRender::class;

    protected $renderOptions = [];

    protected $sources;

    public function __construct(
        protected array $config,
        protected ImageContract $factory,
        protected ?array $glideParams = null
    ) {
        $preparedConfig = (new ImageSetConfig($config))->get();

        $this->sources = new ImageSetSources($preparedConfig, $factory, $glideParams);
    }

    public function data(): array
    {
        return [
            'sources' => $this->sources->get(),
            'lqip' => $this->sources->lqip(),
        ];
    }

    public function toArray(): array
    {
        return [
            'sources' => $this->sources->toArray(),
            'lqip' => $this->sources->lqip()->toArray(),
        ];
    }

    public function setRenderClass(string $cls): void
    {
        $this->renderClass = $cls;
    }

    public function setRenderOptions(array $options): void
    {
        $this->renderOptions = $options;
    }

    public function render(): ImageSetRender
    {
        return new $this->renderClass($this, $this->renderOptions);
    }
}
