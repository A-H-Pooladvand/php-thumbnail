<?php

use Thumb\Dev\Whoops\Whoops;

class Boot
{
    private $boots = [
        Whoops::class,
    ];

    /**
     * Fire off bootstrap setting.
     *
     * @return void
     */
    public function start(): void
    {
        foreach ($this->boots as $boot) {
            (new $boot)->handle();
        }
    }
}