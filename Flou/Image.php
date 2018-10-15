<?php
namespace Flou;

use Flou\Path;

class Image {
    private $base_path;
    private $base_url;
    private $original_file;
    private $default_processed_file;

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
        $this->_setDefaultProcessedFile();
        $this->is_processed = false;
        return $this;
    }

    private function _setDefaultProcessedFile() {
        $filename = Path::filename($this->original_file);
        $extension = Path::extension($this->original_file);
        $this->default_processed_file = "{$filename}.flou.{$extension}";
    }

    public function process() {
        // TODO add support for custom output path...

        if (!$this->isProcessed()) {
            $input_file = $this->getOriginalFilePath();
            $output_file = $this->getProcessedFilePath();
            // TODO throw NotConfigured exception if no file is loaded?

            $this->_processFile($input_file, $output_file);
            $this->is_processed = true;
        }
        return $this;
    }

    private function _processFile($input_file, $output_file) {
        // TODO add config for resize width and blur radius

        $image = new \Imagick($input_file);
        $geometry = $image->getImageGeometry();
        $width = 40;
        $height = $width * $geometry["height"] / $geometry["width"];

        $resize = $image ? $image->adaptiveResizeImage($width, $height, true) : null;
        $blur = $resize ? $image->adaptiveBlurImage(10, 10) : null;
        $write = $blur ? $image->writeImage($output_file) : null;

        if (!$resize) {
            throw new \Exception("Resize failed: $input_file");
        }
        if (!$blur) {
            throw new \Exception("Blur failed: $input_file");
        }
        if (!$resize) {
            throw new \Exception("Write failed: $input_file");
        }
    }

    public function isProcessed() {
        if ($this->is_processed) {
            return true;
        }
        $file_path = $this->getProcessedFilePath();
        if (file_exists($file_path)) {
            return true;
        }
        return false;
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

    public function getHTML($alt="") {
        // TODO add support for custom output path...

        $original_url = $this->getOriginalURL();
        $processed_url = $this->getProcessedURL();
        $template = ( 
            '<div class="flou-container">' .
                sprintf('<img class="flou-processed-image" src="%s" alt="" />',
                    $processed_url) .
                sprintf('<img class="flou-original-image" src="%s" alt="%s" />',
                    $original_url, $alt) .
            '</div>'
        );
        if ($original_url && $processed_url) {
            return $template;
        }

        // TODO throw NotConfigured exception?
        return null;
    }

    public function getOriginalFilePath() {
        if ($this->base_path && $this->original_file) {
            return Path::join($this->base_path, $this->original_file);
        }
        return null;
    }

    public function getProcessedFilePath() {
        if ($this->base_path && $this->default_processed_file) {
            return Path::join($this->base_path, $this->default_processed_file);
        }
        return null;
    }

    public function getOriginalURL() {
        if ($this->base_url && $this->original_file) {
            return "{$this->base_url}/{$this->original_file}";
        }
        return null;
    }

    public function getProcessedURL() {
        if ($this->base_url && $this->default_processed_file) {
            return "{$this->base_url}/{$this->default_processed_file}";
        }
        return null;
    }
}
