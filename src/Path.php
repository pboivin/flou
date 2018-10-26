<?php
namespace Flou;

use Flou\Exception\InvalidFileException;
use Flou\Exception\InvalidDirectoryException;

class Path
{
    public static function join(...$parts)
    {
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    public static function validateFile($path)
    {
        if (!file_exists($path)) {
            throw new InvalidFileException("path does not exist: $path");
        }
        if (!is_file($path)) {
            throw new InvalidFileException("path is not a file: $path");
        }
    }

    public static function validateDirectory($path)
    {
        if (!file_exists($path)) {
            throw new InvalidDirectoryException("path does not exist: $path");
        }
        if (!is_dir($path)) {
            throw new InvalidDirectoryException("path is not a directory: $path");
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
