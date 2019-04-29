<?php

namespace Thumb\lib\Config;

class Config
{
    public function handle(string $file)
    {
        $pieces = explode('.', $file);

        $fileName = $pieces[0];
        unset($pieces[0]);

        $config = require config_dir().'/'.$fileName.'.php';

        foreach ($pieces as $index => $piece) {
            $config = $config[$piece];
        }

        return $config;
    }
}