<?php

namespace Pboivin\Flou;

class ImageFile
{
    protected $size;

    final public function __construct(
        protected string $fileName,
        protected string $path,
        protected string $url,
        protected ?ImageFileInspector $inspector = null
    ) {
    }

    public static function fromArray(array $data): static
    {
        $obj = new static(
            $data['fileName'] ?? '',
            $data['path'] ?? '',
            $data['url'] ?? '',
        );

        $obj->size = [
            'width' => $data['width'] ?? 0,
            'height' => $data['height'] ?? 0,
        ];

        return $obj;
    }

    public function fileName(): string
    {
        return $this->fileName;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function width(): int
    {
        return $this->size('width');
    }

    public function height(): int
    {
        return $this->size('height');
    }

    public function ratio(): float
    {
        if (!$width = $this->width()) {
            return 0.0;
        }

        if (!$height = $this->height()) {
            return 0.0;
        }

        return (float) $width / $height;
    }

    protected function size(string $param): int
    {
        if (!$this->size && $this->inspector) {
            $this->size = $this->inspector->getSize($this->path);
        }

        return $this->size[$param] ?? 0;
    }

    public function toArray(): array
    {
        return [
            'fileName' => $this->fileName(),
            'path' => $this->path(),
            'url' => $this->url(),
            'width' => $this->width(),
            'height' => $this->height(),
            'ratio' => $this->ratio(),
        ];
    }

    public function toBase64String(): string
    {
        if ($this->inspector) {
            return $this->inspector->base64Encode($this->path);
        }

        // transparent fallback
        return 'data:image/gif;base64,R0lGODdhAQABAIAAAP///////ywAAAAAAQABAAACAkQBADs=';
    }
}
