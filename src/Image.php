<?php
namespace Flou;

use Flou\ImageProcessorInterface;
use Flou\ImageRendererInterface;
use Flou\DefaultImageProcessor;
use Flou\DefaultImageRenderer;
use Flou\Path;

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
    private $is_processed;
    private $image_renderer;
    private $image_processor;


    /**
     * Loads the image file to be processed.
     *
     * If $base_path is set, $original_file is expected to be a filename to be
     * found in that directory. Otherwise, $original_file should be a full path
     * to the image, from which $base_path is extracted for future reference.
     *
     * @param string $original_file The filename or full path to the image.
     * @return Image $this
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
     * @return Image $this
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
     * @return Image $this
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
     * Process the image.
     *
     * @param bool $force_process Regenerate the image if it already exists.
     */
    private function internalProcess($force_process=false)
    {
        // TODO add getImageProcessor

        if (!$this->image_processor) {
            $this->setImageProcessor(new DefaultImageProcessor());
        }

        $this->image_processor->setImage($this);

        if ($force_process or !$this->processedFileExists()) {
            $this->image_processor->process();
        }
        $this->is_processed = true;
    }

    /**
     * Whether the image has already been processed;
     *
     * @return bool
     */
    public function isProcessed()
    {
        return $this->is_processed;
    }

    /**
     * Whether the processed file exists.
     *
     * @return bool
     */
    private function processedFileExists()
    {
        $file_path = $this->getProcessedFilePath();
        return file_exists($file_path);
    }

    /**
     * Sets $base_path.
     *
     * @param string $base_path
     * @return Image $this
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
     * @return Image $this
     * @throws InvalidDirectoryException If the path isn't a directory or doesn't exist.
     * @see getProcessedFilePath()
     */
    public function setProcessedPath($processed_path)
    {
        Path::validateDirectory($processed_path);
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
     * @return Image $this
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
     * @return Image $this
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
     * @return Image $this
     * @see getProcessedURL()
     */
    public function setProcessedUrl($processed_url)
    {
        $this->custom_processed_url = $processed_url;
        return $this;
    }

    /**
     * Sets the image processor to be used for processing.
     *
     * @param ImageProcessorInterface $image_processor
     * @return Image $this
     * @see internalProcess()
     */
    public function setImageProcessor(ImageProcessorInterface $image_processor)
    {
        $this->image_processor = $image_processor;
        return $this;
    }

    /**
     * Sets the image renderer to be used for rendering.
     *
     * @param ImageRendererInterface $image_renderer
     * @return Image $this
     * @see render()
     */
    public function setImageRenderer(ImageRendererInterface $image_renderer)
    {
        $this->image_renderer = $image_renderer;
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
            if ($processed_file) {
                return Path::join($base_path, $processed_file);
            }
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

        if ($this->custom_processed_path xor $this->custom_processed_url) {
            // TODO maybe trow an NotConfiguredException for such requirements...
            return null;
        }
        if ($this->custom_processed_url) {
            $base_url = $this->custom_processed_url;
        }
        if ($base_url) {
            $processed_file = $this->default_processed_file;

            if ($this->custom_processed_file) {
                $processed_file = $this->custom_processed_file;
            }
            if ($processed_file) {
                return "{$base_url}/{$processed_file}";
            }
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
        if ($this->is_processed) {
            return $this->image_processor->getOriginalWidth();
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
        if ($this->is_processed) {
            return $this->image_processor->getOriginalHeight();
        }
        return null;
    }

    /**
     * Returns the output from and image renderer to display an image.
     * DefaultImageRenderer is used if no other image renderer was configured.
     *
     * @param string $description The image description.
     * @return string|null
     */
    public function render($description="")
    {
        // TODO add getImageRenderer

        if (!$this->image_renderer) {
            $this->setImageRenderer(new DefaultImageRenderer());
        }

        return $this->image_renderer
            ->setImage($this)
            ->setDescription($description)
            ->render();
    }
}
