<?php

namespace Pboivin\Flou;

use InvalidArgumentException;
use Stringable;

abstract class ImgRenderable implements Stringable
{
    use RendersHtml;

    protected $baseClass = 'lazyload';

    protected $wrapperClass = 'lazyload-wrapper';

    protected $lqipClass = 'lazyload-lqip';

    protected $paddingClass = 'lazyload-padding';

    protected $aspectRatio = false;

    protected $paddingTop = false;

    protected $wrapper = false;

    protected $base64Lqip = false;

    /* Internal */
    protected $includeLqip = true;

    protected function acceptRenderConfig(array $config): void
    {
        foreach ($config as $key => $value) {
            if (method_exists($this, $method = "set{$key}")) {
                $this->$method($value);
            } elseif (method_exists($this, $method = "use{$key}")) {
                $this->$method($value);
            } else {
                throw new InvalidArgumentException("Invalid option '$key'.");
            }
        }
    }

    public function __toString(): string
    {
        return $this->img();
    }

    public function setBaseClass(string $cssClass): self
    {
        $this->baseClass = $cssClass;

        return $this;
    }

    public function setWrapperClass(string $cssClass): self
    {
        $this->wrapperClass = $cssClass;

        return $this;
    }

    public function setLqipClass(string $cssClass): self
    {
        $this->lqipClass = $cssClass;

        return $this;
    }

    public function setPaddingClass(string $cssClass): self
    {
        $this->paddingClass = $cssClass;

        return $this;
    }

    public function useWrapper(bool $value = true): self
    {
        $this->wrapper = $value;

        return $this;
    }

    public function useAspectRatio(mixed $value = true): self
    {
        if ($value === true) {
            $this->aspectRatio = $this->main()->ratio() ?: 1;
        } elseif ($value === false) {
            $this->aspectRatio = false;
        } else {
            $this->aspectRatio = $value;
        }

        return $this;
    }

    public function usePaddingTop(mixed $value = true): self
    {
        $this->useAspectRatio($value);

        $this->paddingTop = !!$value;

        return $this;
    }

    public function useBase64Lqip(bool $value = true): self
    {
        $this->base64Lqip = $value;

        return $this;
    }

    public function lqipUrl(): string
    {
        if ($this->base64Lqip) {
            return $this->lqip()->toBase64String();
        }

        return $this->lqip()->url();
    }

    abstract public function main(): ImageFile;

    abstract public function lqip(): ImageFile;

    abstract public function img(array $attributes = []): string;

    abstract public function noScript(array $attributes = []): string;

    protected function prepareAttributes(array $attributes = []): array
    {
        $style = [];

        if ($this->paddingTop && $this->aspectRatio) {
            $style[] = $this->collectStyles([
                'position' => 'absolute',
                'top' => '0',
                'left' => '0',
                'width' => '100%',
                'height' => '100%',
                'object-fit' => 'cover',
                'object-position' => 'center',
            ]);
        } elseif ($this->aspectRatio) {
            $style[] = $this->collectStyles([
                'aspect-ratio' => $this->aspectRatio,
                'object-fit' => 'cover',
                'object-position' => 'center',
            ]);
        }

        if ($attributes['style'] ?? false) {
            $style[] = $attributes['style'];
        }

        if ($style) {
            $attributes['style'] = implode(' ', $style);
        }

        $attributes['class'] = $this->collectClasses([
            $this->baseClass,
            $attributes['class'] ?? '',
        ]);

        $attributes['alt'] = $attributes['alt'] ?? '';

        return $attributes;
    }

    protected function handleAttributeOverrides(array $attributes = []): array
    {
        // attribute overrides start with a '!' (e.g. `['!src' => false]`)
        $attributeOverrides = array_filter(array_keys($attributes), fn ($key) => $key[0] === '!');

        foreach ($attributeOverrides as $override) {
            $name = substr($override, 1);
            $value = $attributes[$override];

            if ($value === false) {
                unset($attributes[$name]);
            } elseif ($value === true) {
                $attributes[$name] = "";
            } else {
                $attributes[$name] = $value;
            }

            unset($attributes[$override]);
        }

        return $attributes;
    }

    protected function renderImg(array $attributes = []): string
    {
        $img = $this->htmlTag('img', $attributes);
        $img = $this->handlePaddingTop($img);
        $img = $this->handleWrapper($img);

        return $img;
    }

    protected function handlePaddingTop(string $input): string
    {
        if ($this->paddingTop && $this->aspectRatio) {
            return $this->htmlWrap(
                'div',
                [
                    'class' => $this->paddingClass,
                    'style' => $this->collectStyles([
                        'position' => 'relative',
                        'padding-top' => $this->getPaddingTopValue((float) $this->aspectRatio),
                    ]),
                ],
                $input
            );
        }

        return $input;
    }

    protected function handleWrapper(string $input): string
    {
        if ($this->wrapper) {
            return $this->htmlWrap(
                'div',
                [
                    'class' => $this->wrapperClass,
                ],
                implode('', [
                    $input,
                    $this->includeLqip
                        ? $this->htmlTag('img', [
                            'class' => $this->lqipClass,
                            'src' => $this->lqipUrl(),
                        ])
                        : '',
                ])
            );
        }

        return $input;
    }

    protected function getPaddingTopValue(float $aspectRatio): string
    {
        $padding = (1 / $aspectRatio) * 100;

        $padding = number_format($padding, 2);

        // remove decimals if number is not really a float
        $padding = preg_replace('/\.0+$/', '', "$padding");

        return "{$padding}%";
    }

    protected function resolveImageFile(array|Image $item, bool $cached = false): ImageFile
    {
        $role = $cached ? 'cached' : 'source';

        if (is_array($item)) {
            return ImageFile::fromArray($item[$role]);
        }

        return $item->$role();
    }

    protected function handleSizes($attributes, ImageFile $imageFile): array
    {
        if ($width = $imageFile->width()) {
            $attributes['width'] = $width;
        }

        if ($height = $imageFile->height()) {
            $attributes['height'] = $height;
        }

        return $attributes;
    }
}
