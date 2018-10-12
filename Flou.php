<?php
namespace Flou;

class InvalidFile extends \Exception {}
class InvalidDirectory extends \Exception {}

class Path {
    public static function join(...$parts) {
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    public static function checkFile($path) {
        if (!file_exists($path)) {
            throw new InvalidFile("path does not exist: $path");
        }
        if (!is_file($path)) {
            throw new InvalidFile("path is not a file: $path");
        }
    }

    public static function checkDirectory($path) {
        if (!file_exists($path)) {
            throw new InvalidDirectory("path does not exist: $path");
        }
        if (!is_dir($path)) {
            throw new InvalidDirectory("path is not a directory: $path");
        }
    }
}

class Image {
    private $base_path;
    private $base_url;
    private $original_file;
    private $processed_file;

    public function __constructor() {
    }

    public function load($original_file) {
        $file_path = Path::join($this->base_path, $original_file);
        Path::checkFile($file_path);
        $this->original_file = $original_file;
        return $this;
    }

    public function process($processed_file=null) {
        return $this;
    }

    public function isProcessed() {
    }

    public function setBasePath($base_path) {
        Path::checkDirectory($base_path);
        $this->base_path = $base_path;
        return $this;
    }

    public function setBaseUrl($base_url) {
        return $this;
    }

    public function getHTML($original_url=null, $processed_url=null) {
        return $this;
    }

    public function getOriginalFilePath() {
        if ($this->base_path && $this->original_file) {
            return Path::join($this->base_path, $this->original_file);
        }
        return null;
    }

    public function getProcessedFilePath() {
    }

    public function getOriginalURL() {
    }

    public function getProcessedURL() {
    }
}
