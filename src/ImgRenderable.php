<?php

namespace Pboivin\Flou;

abstract class ImgRenderable
{
    protected $baseClass = 'lazyload';

    protected $wrapperClass = 'lazyload-wrapper';

    protected $lqipClass = 'lazyload-lqip';

    protected $paddingClass = 'lazyload-padding';

    protected $aspectRatio = false;

    protected $paddingTop = false;

    protected $wrapper = false;

    protected $includeLqip = true;

    public function setBaseClass(string $cls): self
    {
        $this->baseClass = $cls;

        return $this;
    }

    public function setWrapperClass(string $cls): self
    {
        $this->wrapperClass = $cls;

        return $this;
    }

    public function setLqipClass(string $cls): self
    {
        $this->lqipClass = $cls;

        return $this;
    }

    public function useWrapper(bool $value = true): self
    {
        $this->wrapper = $value;

        return $this;
    }

    public function useAspectRatio(?float $value = null): self
    {
        $this->aspectRatio = is_null($value) ? $this->source()->ratio() : $value;

        return $this;
    }

    public function usePaddingTop(?float $value = null): self
    {
        $this->useAspectRatio($value);

        $this->paddingTop = true;

        return $this;
    }

    abstract public function source(): ImageFile;

    abstract public function lqip(): ImageFile;

    abstract public function img(array $attributes = []): string;

    abstract public function noScript(array $attributes = []): string;

    protected function prepareAttributes(array $attributes = []): array
    {
        $style = [];

        if ($this->paddingTop && $this->aspectRatio) {
            $style[] =
                'position: absolute; top: 0; height:0; width: 100%; height: 100%; object-fit: cover; object-position: center;';
        } elseif ($this->aspectRatio) {
            $style[] = "aspect-ratio: {$this->aspectRatio};";
        }

        if ($attributes['style'] ?? false) {
            $style[] = $attributes['style'];
        }

        if ($style) {
            $attributes['style'] = implode(' ', $style);
        }

        $attributes['class'] = implode(' ', [$this->baseClass, $attributes['class'] ?? '']);

        $attributes['alt'] = $attributes['alt'] ?? '';

        return $attributes;
    }

    protected function renderImg(array $attributes = []): string
    {
        $img = $this->imgTag($attributes);

        if ($this->paddingTop && $this->aspectRatio) {
            $padding = (1 / $this->aspectRatio) * 100;
            $classAttr = 'class="' . $this->paddingClass . '"';
            $styleAttr = 'style="position: relative; padding-top: ' . $padding . '%;"';
            $img = "<div {$classAttr} {$styleAttr}>{$img}</div>";
        }

        if ($this->wrapper) {
            return implode('', [
                '<div class="' . $this->wrapperClass . '">',
                $img,
                $this->includeLqip
                    ? $this->imgTag([
                        'class' => $this->lqipClass,
                        'src' => $this->lqip()->url(),
                    ])
                    : '',
                '</div>',
            ]);
        }

        return $img;
    }

    protected function imgTag(array $attributes = []): string
    {
        return $this->tag('img', $attributes);
    }

    protected function tag(string $tag, array $attributes = [], mixed $content = false): string
    {
        $attributesOutput = [];

        foreach ($attributes as $key => $value) {
            $attributesOutput[] = $key . '="' . $value . '"';
        }

        if ($content === false) {
            return "<{$tag} " . implode(' ', $attributesOutput) . '>';
        }

        if ($content === true) {
            return "<{$tag} " . implode(' ', $attributesOutput) . ' />';
        }

        return "<{$tag} " . implode(' ', $attributesOutput) . ">{$content}<{$tag}>";
    }
}
