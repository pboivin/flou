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

    public function base64Encode(string $file): string
    {
        $type = pathinfo($file, PATHINFO_EXTENSION);

        $data = file_get_contents($file);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
