<?php
namespace Flou;

use Flou\Path;

class Image {
    private $base_path;
    private $base_url;
    private $original_file;
    private $processed_file;

    public function __constructor() {
    }

    public function load($original_file) {
        if ($this->base_path) {
            // original_file is to be found in base_path
            $file_path = Path::join($this->base_path, $original_file);
            Path::checkFile($file_path);
            $this->original_file = $original_file;
        } else {
            // original_file is a complete path, extract base_path from it
            $file_path = $original_file;
            Path::checkFile($file_path);
            $this->base_path = dirname($file_path);
            $this->original_file = basename($file_path);
        }
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
        $this->base_url = $base_url;
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
