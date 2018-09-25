<?php

/**
 * Static class for loading images progressively
 */

class ProgressiveImages
{
    private static $config = null;
    private static $FORCE_REGENERATE = false;

    /**
     * Initialize class configuration
     */
    public static function init($config=[]) {
        $tmp_config = [
            "blur_path" => "",
            "blur_url" => "",
            "base_path" => "",
            "base_url" => "",
        ];
        self::$config = array_merge($tmp_config, $config);
    }

    /**
     * Generate HTML and Javascript code for displaying images
     */
    public static function generate($url, $container_class) {
        if (!$url) return null;

        $output = $url;
        $is_gif = preg_match('#\.gif$#', $url);
        $url_parts = explode("/", $url);
        $filename = array_pop($url_parts);
        $do_generate = !$is_gif && $filename;

        if ($do_generate) {
            $src_file = self::$config['base_path'] . '/' . $filename;
            $dest_file = self::$config['blur_path'] . '/' . $filename;
            $dest_url = self::$config['blur_url'] . '/' . $filename;

            $blurred = self::_scale_and_blur_image($src_file, $dest_file);
            $output = $blurred ? $dest_url : $url;
        }

        $id = 'progressiveimage' . uniqid();

        return self::_get_javascript($id, $url) .
            '<div id="' . $id . '" class="progressive-images ' . $container_class . '">' .
                '<img class="progressive-images__blur" src="' . $output . '">' .
                '<img onload="fn_'.$id.'()" class="progressive-images__original" src="' . $url . '">' .
            '</div>';
    }

    /**
     * Generate an img tag for the original image only
     * May be used as a fallback
     */
    public static function getOriginal($url, $container_class) {
        return '<img class="' . $container_class . '" src="' . $url . '">';
    }

    /**
     * Check if the blurred image exists
     * If it doesn't, try to generate it
     */
    private static function _scale_and_blur_image($src, $dest) {
        $BLUR_SIZE = 11;
        $do_generate = ( !file_exists($dest) || self::$FORCE_REGENERATE );

        if ($do_generate) {
            $image = new Imagick($src);
            $resize = $image ? $image->adaptiveResizeImage($BLUR_SIZE, $BLUR_SIZE, true) : null;
            $blur = $resize ? $image->adaptiveBlurImage(10, 10) : null;
            $write = $blur ? $image->writeImage($dest) : null;
#           if (!$resize) die('RESIZE FAILED');
#           if (!$blur) die('BLUR FAILED');
#           if (!$write) die('WRITE FAILED');

            return $write;
        }
        return true;
    }

    /**
     * Get the geometry of the original image
     */
    private static function _get_geometry($src) {
        if (file_exists($src) ) {
            $image = new Imagick($src);
            return $image->getImageGeometry();
        }
        return false;
    }

    /**
     * Generate the Javascript snippet for a particular instance
     */
    private static function _get_javascript($id, $url) {
        $func_name = 'fn_' . $id;
        return "<script> function $func_name(){ var img = document.getElementById(\"$id\"); img.className = img.className + \" is-loaded\"; } </script>";
    }
}
