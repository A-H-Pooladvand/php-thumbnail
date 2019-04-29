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
 * @param string $file
 */
if (! function_exists('thumb')) {
    function thumb(string $path, int $width = null, int $height = null, string $mode = null)
    {
        Thumb::make($path, $width, $height, $mode);
    }
}