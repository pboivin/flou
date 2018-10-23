<?php
namespace Flou;

use Flou\Exception\InvalidFile;
use Flou\Exception\InvalidDirectory;

class Path
{
    public static function join(...$parts)
    {
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    public static function checkFile($path)
    {
        if (!file_exists($path)) {
            throw new InvalidFile("path does not exist: $path");
        }
        if (!is_file($path)) {
            throw new InvalidFile("path is not a file: $path");
        }
    }

    public static function checkDirectory($path)
    {
        if (!file_exists($path)) {
            throw new InvalidDirectory("path does not exist: $path");
        }
        if (!is_dir($path)) {
            throw new InvalidDirectory("path is not a directory: $path");
        }
    }

    public static function filename($path)
    {
        $info = pathinfo($path);
        if (isset($info["filename"])) {
            return $info["filename"];
        }
        return "";
    }

    public static function extension($path)
    {
        $info = pathinfo($path);
        if (isset($info["extension"])) {
            return $info["extension"];
        }
        return "";
    }
}
