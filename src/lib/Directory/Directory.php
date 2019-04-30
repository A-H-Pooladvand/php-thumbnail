<?php

namespace Thumb\lib\Directory;

/**
 * Class Directory
 *
 * @author Amirhossein Pooladvand
 * @package Thumb\lib\Directory
 */
class Directory
{
    /**
     * Determines base director (root of the project).
     *
     * @return string
     */
    public function base(): string
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }

    /**
     * Determines public directory
     *
     * @param string $path
     *
     * @return string
     */
    public function public(string $path = null): string
    {
        return $this->base().'/public'.($path ? '/'.$path : $path);
    }

    /**
     * Determines config directory
     *
     * @return string
     */
    public function config(): string
    {
        return $this->base().'/config';
    }
}
