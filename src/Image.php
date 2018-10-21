<?php
namespace Flou;

use Flou\Path;
use Flou\Exception\ProcessError;

class Image {
    private $base_path;
    private $base_url;
    private $original_file;
    private $default_processed_file;
    private $custom_processed_file;
    private $custom_processed_path;
    private $custom_processed_url;
    private $original_geometry;
    private $is_processed;

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

    private function _internalProcess($force_process=false) {
        $input_file = $this->getOriginalFilePath();
        $imagick_image = new \Imagick($input_file);
        $this->original_geometry = $imagick_image->getImageGeometry();

        if ($force_process or !$this->isProcessed()) {
            $this->_processImage($imagick_image);
            $this->is_processed = true;
        }
    }

    public function process() {
        $this->_internalProcess();
        return $this;
    }

    public function forceProcess() {
        $output_file = $this->getProcessedFilePath();
        if (file_exists($output_file)) {
            unlink($output_file);
        }
        $this->_internalProcess(true);
        return $this;
    }

    private function _processImage($imagick_image) {
        $input_file = $this->getOriginalFilePath();
        $output_file = $this->getProcessedFilePath();
        $geometry = $this->original_geometry;
        $resize_width = 40;
        $resize_height = $resize_width * $geometry["height"] / $geometry["width"];
        // TODO add config for resize width and blur radius

        $resized = $imagick_image->adaptiveResizeImage($resize_width, $resize_height, true);
        if (!$resized) {
            throw new ProcessError("Resize failed: $input_file");
        }
        $blurred = $imagick_image->adaptiveBlurImage(10, 10);
        if (!$blurred) {
            throw new ProcessError("Blur failed: $input_file");
        }
        $written = $imagick_image->writeImage($output_file);
        if (!$written) {
            throw new ProcessError("Write failed: $input_file");
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

    public function setProcessedPath($processed_path) {
        $this->custom_processed_path = $processed_path;
        return $this;
    }

    public function setProcessedFile($processed_file) {
        $this->custom_processed_file = $processed_file;
        return $this;
    }

    public function setBaseUrl($base_url) {
        $this->base_url = $base_url;
        return $this;
    }

    public function setProcessedUrl($processed_url) {
        $this->custom_processed_url = $processed_url;
        return $this;
    }

    public function getOriginalFilePath() {
        if ($this->base_path && $this->original_file) {
            return Path::join($this->base_path, $this->original_file);
        }
        return null;
    }

    public function getProcessedFilePath() {
        $base_path = $this->base_path;

        if ($this->custom_processed_path) {
            $base_path = $this->custom_processed_path;
        }
        if ($base_path) {
            $processed_file = $this->default_processed_file;

            if ($this->custom_processed_file) {
                $processed_file = $this->custom_processed_file;
            }
            return Path::join($base_path, $processed_file);
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
        $base_url = $this->base_url;

        if ($this->custom_processed_path) {
            if ($this->custom_processed_url) {
                $base_url = $this->custom_processed_url;
            } else {
                // custom_processed_url is required when using custom_processed_path
                return null;
            }
        }
        if ($base_url) {
            $processed_file = $this->default_processed_file;

            if ($this->custom_processed_file) {
                $processed_file = $this->custom_processed_file;
            }
            return "{$base_url}/{$processed_file}";
        }
        return null;
    }

    public function getOriginalWidth() {
        if ($this->original_geometry) {
            return $this->original_geometry["width"];
        }
        return null;
    }

    public function getOriginalHeight() {
        if ($this->original_geometry) {
            return $this->original_geometry["height"];
        }
        return null;
    }

    public function getHTML($alt="") {
        $container_class = "flou-container";
        $img_class = "flou-image";
        $width = $this->getOriginalWidth();
        $height = $this->getOriginalHeight();
        $processed_url = $this->getProcessedURL();
        $original_url = $this->getOriginalURL();

        $template = sprintf(
            '<div class="%s">' .
                '<img class="%s" width="%s" height="%s" src="%s" data-original="%s" alt="%s" />' .
            '</div>',
            $container_class, $img_class, $width, $height, $processed_url, $original_url, $alt
        );

        if ($original_url && $processed_url) {
            return $template;
        }
        return null;
    }
}
