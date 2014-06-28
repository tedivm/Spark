<?php

namespace Spark;

class Resources
{
    protected $base;
    protected $types = array('plugins' => 'src/Spark/Plugins/', 'config' => 'config/');

    public function __construct()
    {
        $this->base = __DIR__ . '/../../';

    }

    public function getPath($type)
    {
        if (!isset($this->types[$type])) {
            return false;
        }

        return $this->base . $this->types[$type];
    }
}
