<?php

namespace Pboivin\Flou;

class ImageFormats
{
    protected static $formatTypes = [
        'avif' => 'image/avif',
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'pjpg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'tiff' => 'image/tiff',
    ];

    public static function getType(string $format): ?string
    {
        return self::$formatTypes[$format] ?? null;
    }
}
