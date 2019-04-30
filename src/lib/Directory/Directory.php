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
     * @param string|null $path
     * @return string
     */
    public function base(string $path = null): string
    {
        return $_SERVER['DOCUMENT_ROOT'].($path ? '/'.$path : $path);
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
