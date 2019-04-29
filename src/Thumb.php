<?php

namespace Thumb;

use Intervention\Image\ImageManager;

/**
 * Class Thumb
 *
 * @author Amirhossein Pooladvand
 *
 * @mixin \Thumb\Img
 * @package Thumb
 */
class Thumb
{
    /**
     * Thumbnail maker instance.
     *
     * @return \Thumb\Img
     */
    private function newImg(): Img
    {
        return new Img($this->newIntervention());
    }

    /**
     * Image intervention instance.
     *
     * @return \Intervention\Image\ImageManager
     */
    private function newIntervention(): ImageManager
    {
        return new ImageManager();
    }

    /**
     * __call magic method.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->newImg()->{$method}(...$parameters);
    }

    /**
     * __callStatic magic method.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}