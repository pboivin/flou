<?php
namespace Flou;

class Image {
    private $base_path;
    private $base_url;
    private $original_file;
    private $processed_file;

    public function __constructor() {
    }

    public function load($original_file) {
    }

    public function process($processed_file=null) {
    }

    public function isProcessed() {
    }

    public function setBasePath($base_path) {
        $this->base_path = $base_path;
        echo "Got base_path = " . $base_path . "\n";
    }

    public function setBaseUrl($base_url) {
    }

    public function getHTML($original_url=null, $processed_url=null) {
    }

    public function getOriginalFile() {
    }

    public function getProcessedFile() {
    }

    public function getOriginalURL() {
    }

    public function getProcessedURL() {
    }
}
