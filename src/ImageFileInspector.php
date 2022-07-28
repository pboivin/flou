<?php

namespace Pboivin\Flou;

class ImageFileInspector
{
    protected $sizes = [];

    public function getSize(string $file): array
    {
        if (isset($this->sizes[$file])) {
            return $this->sizes[$file];
        }

        if (file_exists($file)) {
            $size = getimagesize($file);

            [$width, $height] = $size;
        }

        $values = [
            'width' => $width ?? 0,
            'height' => $height ?? 0,
        ];

        if ($size ?? false) {
            $this->sizes[$file] = $values;
        }

        return $values;
    }
}
