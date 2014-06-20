<?php

namespace Spark;


class Resources
{
    protected $base;
    protected $types = array('templates' => 'templates/', 'config' => 'config/');

    public function __construct()
    {
        $this->base = realpath(__DIR__ . '/../../') . '/';

    }

    public function getPath($type)
    {
        if(!isset($this->types[$type])) {
            return false;
        }

        return $this->base . $this->types[$type];
    }
}