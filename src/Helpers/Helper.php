<?php

use Thumb\Thumb;
use Thumb\lib\Config\Config;
use Thumb\lib\Directory\Directory;

/**
 * Determines root directory (project directory).
 */
if (! function_exists('base_dir')) {
    function base_dir()
    {
        return (new Directory)->base();
    }
}

/**
 * Determines public directory folder.
 *
 * @param string $path
 */
if (! function_exists('public_dir')) {
    function public_dir(string $path = null)
    {
        return (new Directory)->public($path);
    }
}

/**
 * Determines config directory folder.
 */
if (! function_exists('config_dir')) {
    function config_dir()
    {
        return (new Directory)->config();
    }
}

/**
 * Determines config directory folder.
 *
 * @param string $file
 *
 * @return string
 */
if (! function_exists('config')) {
    function config(string $file)
    {
        return (new Config)->handle($file);
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
if (! function_exists('thumb')) {
    function thumb(string $path, int $width = null, int $height = null, string $mode = null, int $quality = null)
    {
        return Thumb::make($path, $width, $height, $mode, $quality);
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
