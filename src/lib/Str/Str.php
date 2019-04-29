<?php

namespace Thumb\lib\Str;

class Str
{
    private const BEGIN_OF_STRING = 0;

    /**
     * Cuts file path without it's file name.
     * e.g. /user/profile/user.txt = /usr/profile
     *
     * @param string $string
     *
     * @return bool|string
     */
    public static function withoutFileName(string $string)
    {
        return substr(
            $string, static::BEGIN_OF_STRING, static::lastSlashPosition($string)
        );
    }

    /**
     * Determines last occurrence of slash in a string.
     *
     * @param string $string
     *
     * @return int
     */
    public static function lastSlashPosition(string $string): int
    {
        return strrpos($string, '/');
    }
}