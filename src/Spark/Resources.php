<?php

namespace Spark;

class Resources
{
    protected $base;
    protected $directories = array('plugins' => 'src/Spark/Plugins/', 'config' => 'config/');

    public function __construct()
    {
        $this->base = __DIR__ . '/../../';

    }

    public function getPath($type)
    {
        if (!isset($this->directories[$type])) {
            return false;
        }

        return $this->base . $this->directories[$type];
    }
}
