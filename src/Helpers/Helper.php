<?php

use Thumb\Thumb;
use Thumb\lib\Directory\Directory;

/**
 * Determines root directory (project directory).
 */
if (! function_exists('base_dir')) {
    function base_dir(string $path = null)
    {
        return (new Directory)->base($path);
    }
}

/**
 * Helper function for making thumbnails easily.
 *
 * @param string $path
 * @param int $width
 * @param int $height
 * @param string $mode
 * @param int $quality
 *
 * @return string
 */
if (! function_exists('img')) {
    function img(string $path, int $width = null, int $height = null, string $mode = null, int $quality = null)
    {
        return Thumb::make($path, $width, $height, $mode, $quality);
    }
}
