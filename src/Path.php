<?php
namespace Flou;

use Flou\Exception\InvalidFileException;
use Flou\Exception\InvalidDirectoryException;

/**
 * Flou\Path is a set of static helper methods used to work with paths more easily.
 */
class Path
{
    /**
     * Joins multiple parts into a complete path
     *
     * @param string[] $parts
     * @return string
     */
    public static function join(...$parts)
    {
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    /**
     * Validates that $path exists and is a file.
     *
     * @param string $path
     * @throws InvalidFileException If the path isn't a file or doesn't exist.
     */
    public static function validateFile($path)
    {
        if (!file_exists($path)) {
            throw new InvalidFileException("path does not exist: $path");
        }
        if (!is_file($path)) {
            throw new InvalidFileException("path is not a file: $path");
        }
    }

    /**
     * Validates that $path exists and is a directory.
     *
     * @param string $path
     * @throws InvalidDirectoryException If the path isn't a directory or doesn't exist.
     */
    public static function validateDirectory($path)
    {
        if (!file_exists($path)) {
            throw new InvalidDirectoryException("path does not exist: $path");
        }
        if (!is_dir($path)) {
            throw new InvalidDirectoryException("path is not a directory: $path");
        }
    }

    /**
     * Returns the prefix part of a filename, stripped from the file extension.
     *
     * @param string $path
     * @return string
     */
    public static function filename($path)
    {
        $info = pathinfo($path);
        if (isset($info["filename"])) {
            return $info["filename"];
        }
        return "";
    }

    /**
     * Returns the extension part of a filename.
     *
     * @param string $path
     * @return string
     */
    public static function extension($path)
    {
        $info = pathinfo($path);
        if (isset($info["extension"])) {
            return $info["extension"];
        }
        return "";
    }
}
