<?php
namespace Flou;

use Flou\Path;
use Flou\ImageRenderer;
use Flou\Exception\ImageProcessException;

/**
 * Flou\Image is the main class. It loads, processes and outputs images suitable to
 * implement lazy-loading on the Web.
 */
class Image
{
    private $base_path;
    private $base_url;
    private $original_file;
    private $default_processed_file;
    private $custom_processed_file;
    private $custom_processed_path;
    private $custom_processed_url;
    private $original_geometry;
    private $is_processed;
    private $resize_width = 40;
    private $blur_radius = 10;
    private $blur_sigma = 10;


    /**
     * Loads the image file to be processed.
     *
     * If $base_path is set, $original_file is expected to be a filename to be
     * found in that directory. Otherwise, $original_file should be a full path
     * to the image, from which $base_path is extracted for future reference.
     *
     * @param string $original_file The filename or full path to the image.
     * @return $this The Flou\Image instance.
     * @throws InvalidFileException If the path isn't a file or doesn't exist.
     */
    public function load($original_file)
    {
        if ($this->base_path) {
            $file_path = Path::join($this->base_path, $original_file);
            Path::validateFile($file_path);
            $this->original_file = $original_file;
        } else {
            $file_path = $original_file;
            Path::validateFile($file_path);
            $this->base_path = dirname($file_path);
            $this->original_file = basename($file_path);
        }
        $this->initDefaultProcessedFile();
        $this->is_processed = false;
        return $this;
    }

    /**
     * Computes the default output filename from $original_file
     */
    private function initDefaultProcessedFile()
    {
        $filename = Path::filename($this->original_file);
        $extension = Path::extension($this->original_file);
        $this->default_processed_file = "{$filename}.flou.{$extension}";
    }

    /**
     * Process the original image if it hasn't been processed yet.
     *
     * @return $this The Flou\Image instance.
     * @see internalProcess()
     */
    public function process()
    {
        $this->internalProcess();
        return $this;
    }

    /**
     * Process the original image, even if it has already been processed. The
     * existing file is deleted and generated again.
     *
     * @return $this The Flou\Image instance.
     * @see internalProcess()
     */
    public function forceProcess()
    {
        $output_file = $this->getProcessedFilePath();
        if (file_exists($output_file)) {
            unlink($output_file);
        }
        $this->internalProcess(true);
        return $this;
    }

    /**
     * Process the image. Saves the original image's geometry to be used in
     * the HTML output.
     *
     * @param bool $force_process Regenerate the image if it already exists.
     */
    private function internalProcess($force_process=false)
    {
        $input_file = $this->getOriginalFilePath();
        $imagick_image = new \Imagick($input_file);
        $this->original_geometry = $imagick_image->getImageGeometry();

        if ($force_process or !$this->isProcessed()) {
            $this->processImagickImage($imagick_image);
            $this->is_processed = true;
        }
    }

    /**
     * Generates a resized and blurred version of an original image then writes
     * the generated image to a file.
     *
     * @param Imagick $imagick_image The original image instance.
     * @throws ImageProcessException If the image can't be processed.
     */
    private function processImagickImage($imagick_image)
    {
        $input_file = $this->getOriginalFilePath();
        $output_file = $this->getProcessedFilePath();
        $geometry = $this->original_geometry;

        $resize_width = $this->resize_width;
        $resize_height = $resize_width * $geometry["height"] / $geometry["width"];
        $resized = $imagick_image->adaptiveResizeImage($resize_width, $resize_height, true);
        if (!$resized) {
            throw new ImageProcessException("Resize failed: $input_file");
        }

        $radius = $this->blur_radius;
        $sigma = $this->blur_sigma;
        $blurred = $imagick_image->adaptiveBlurImage($radius, $sigma);
        if (!$blurred) {
            throw new ImageProcessException("Blur failed: $input_file");
        }

        $written = $imagick_image->writeImage($output_file);
        if (!$written) {
            throw new ImageProcessException("Write failed: $input_file");
        }
    }

    /**
     * Checks whether the $original_image has already been processed.
     *
     * @return bool
     */
    public function isProcessed()
    {
        if ($this->is_processed) {
            return true;
        }
        $file_path = $this->getProcessedFilePath();
        if (file_exists($file_path)) {
            return true;
        }
        return false;
    }

    /**
     * Sets $base_path.
     *
     * @param string $base_path
     * @return $this The Flou\Image instance.
     * @throws InvalidDirectoryException If the path isn't a directory or doesn't exist.
     */
    public function setBasePath($base_path)
    {
        Path::validateDirectory($base_path);
        $this->base_path = $base_path;
        return $this;
    }

    /**
     * Sets $custom_processed_path.
     *
     * By default, the processed image is generated alongside the orinal image,
     * in $base_path. If set, $custom_processed_path is used instead of $base_path
     * as the output directory for the processed image.
     *
     * @param string $processed_path
     * @return $this The Flou\Image instance.
     * @see getProcessedFilePath()
     */
    public function setProcessedPath($processed_path)
    {
        $this->custom_processed_path = $processed_path;
        return $this;
    }

    /**
     * Sets $custom_processed_file.
     *
     * By default, the filename for the processed image is computed from the
     * original filename. If set, $custom_processed_file is used as the filename
     * instead of $default_processed_file.
     *
     * @param string $base_path
     * @return $this The Flou\Image instance.
     * @see getProcessedFilePath()
     * @see getProcessedURL()
     */
    public function setProcessedFile($processed_file)
    {
        $this->custom_processed_file = $processed_file;
        return $this;
    }

    /**
     * Sets $base_url.
     *
     * @param string $base_url
     * @return $this The Flou\Image instance.
     */
    public function setBaseUrl($base_url)
    {
        $this->base_url = $base_url;
        return $this;
    }

    /**
     * Sets $custom_processed_url.
     *
     * @param string $custom_processed_url
     * @return $this The Flou\Image instance.
     * @see getProcessedURL()
     */
    public function setProcessedUrl($processed_url)
    {
        $this->custom_processed_url = $processed_url;
        return $this;
    }

    /**
     * Sets $blur_radius.
     *
     * @param string $value
     * @return $this The Flou\Image instance.
     */
    public function setBlurRadius($value)
    {
        $this->blur_radius = $value;
        return $this;
    }

    /**
     * Sets $blur_sigma.
     *
     * @param string $value
     * @return $this The Flou\Image instance.
     */
    public function setBlurSigma($value)
    {
        $this->blur_sigma = $value;
        return $this;
    }

    /**
     * Sets $resize_width.
     *
     * @param string $value
     * @return $this The Flou\Image instance.
     */
    public function setResizeWidth($value)
    {
        $this->resize_width = $value;
        return $this;
    }

    /**
     * Computes the full path to the original image file.
     *
     * @return string|null
     */
    public function getOriginalFilePath()
    {
        if ($this->base_path && $this->original_file) {
            return Path::join($this->base_path, $this->original_file);
        }
        return null;
    }

    /**
     * Computes the full path to the processed image file.
     *
     * @return string|null
     */
    public function getProcessedFilePath()
    {
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

    /**
     * Computes the full URL to the original image file.
     *
     * @return string|null
     */
    public function getOriginalURL()
    {
        if ($this->base_url && $this->original_file) {
            return "{$this->base_url}/{$this->original_file}";
        }
        return null;
    }

    /**
     * Computes the full URL for the processed image file.
     *
     * @return string|null
     */
    public function getProcessedURL()
    {
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

    /**
     * Returns the with of the original image.
     *
     * @return int|null
     */
    public function getOriginalWidth()
    {
        if ($this->original_geometry) {
            return $this->original_geometry["width"];
        }
        return null;
    }

    /**
     * Returns the height of the original image.
     *
     * @return int|null
     */
    public function getOriginalHeight()
    {
        if ($this->original_geometry) {
            return $this->original_geometry["height"];
        }
        return null;
    }

    /**
     * Returns the output from ImageRenderer::render to display the processed
     * image on a Web page.
     *
     * @param string $alt The alt text to be included in the <img> tag.
     * @return string|null
     */
    public function render($alt="")
    {
        return (new ImageRenderer($this))
            ->setAltText($alt)
            ->render();
    }
}
