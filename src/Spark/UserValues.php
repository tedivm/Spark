<?php

namespace Spark;

class UserValues
{
    protected static $values;

    public function __construct()
    {
        if (!isset(static::$values)) {
            $paths = array();

            $paths[] = '/etc/spark/spark.json';
            $paths[] = $_SERVER['HOME'] . '/.spark.json';
            $paths[] = getcwd() . '/spark.json';

            $values = array();
            foreach ($paths as $file) {
                if (!file_exists($file) || !is_readable($file)) {
                    continue;
                }

                $json = file_get_contents($file);
                $contents = json_decode($json, true);

                if (is_array($contents)) {
                    $values = array_merge_recursive($values, $contents);
                }
            }
            static::$values = $values;
        }
    }

    public function getValue($key)
    {
        return isset(static::$values[$key]) ? static::$values[$key] : null;
    }
}
