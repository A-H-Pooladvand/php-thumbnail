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
    private function newImg(): Img
    {
        return new Img($this->newIntervention());
    }

    private function newIntervention(): ImageManager
    {
        return new ImageManager();
    }

    public function __call(string $method, array $parameters)
    {
        return $this->newImg()->{$method}(...$parameters);
    }

    public static function __callStatic(string $method, array $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}