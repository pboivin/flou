<?php
namespace Flou;

class Path {
    public static function join(...$parts) {
        return implode(DIRECTORY_SEPARATOR, $parts);
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

        if (!file_exists($file_path)) {
            throw new \Exception("File does not exist: $file_path");
        }
        $this->original_file = $original_file;
    }

    public function process($processed_file=null) {
    }

    public function isProcessed() {
    }

    public function setBasePath($base_path) {
        if (!file_exists($base_path)) {
            throw new \Exception("Base path does not exist: $base_path");
        }
        if (!is_dir($base_path)) {
            throw new \Exception("Base path is not a directory: $base_path");
        }
        $this->base_path = $base_path;
    }

    public function setBaseUrl($base_url) {
    }

    public function getHTML($original_url=null, $processed_url=null) {
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
