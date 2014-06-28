<?php

namespace Spark;

use Symfony\Component\Finder\Finder;

class PluginManager
{
    protected static $plugins = array();

    public static function getPluginList()
    {
        if (count(static::$plugins) > 0) {
            return static::$plugins;
        }

        $finder = new Finder();
        $finder->in(__DIR__ . '/Plugins/')
            ->directories()
            ->depth(0)
            ->notName('Core')
            ->ignoreVCS(true)
            ->ignoreDotFiles(true);

        foreach ($finder as $file) {
            static::$plugins[] = $file->getFilename();
        }

        return static::$plugins;
    }

    public static function getPluginObject($plugin)
    {
        $pluginClass = '\\Spark\\Plugins\\' . $plugin . '\\Plugin';

        return new $pluginClass();
    }

}
