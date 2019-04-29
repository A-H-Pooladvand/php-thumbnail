<?php

namespace Thumb\Dev\Whoops;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Whoops
{
    /**
     * Register whoops.
     *
     * @return void
     */
    public function handle(): void
    {
        $whoops = new Run;
        $whoops->pushHandler(new PrettyPageHandler);
        $whoops->register();
    }
}