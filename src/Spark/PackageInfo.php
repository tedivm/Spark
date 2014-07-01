<?php

namespace Spark;

class PackageInfo
{
    protected $name;
    protected $config;

    protected static $json;

    public function __construct($package)
    {
        $this->name = $package;
        $this->config = $this->getPackageConfig();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        if (!isset($this->config['description'])) {
            return false;
        }

        return $this->config['description'];
    }

    public function getPlugins()
    {
        return $this->config['plugins'];
    }

    public function getPackageConfig()
    {
        $resources = new Resources();
        $configPath = $resources->getPath('config');

        $packagesConfig = json_decode(file_get_contents($configPath . 'packages.json'), true);

        if (!isset($packagesConfig)
            || !isset($packagesConfig[$this->name])) {
            throw new \RuntimeException($this->name . ' is not a supported type.');
        }

        $globalConfig = static::getConfig();
        $config = $globalConfig[$this->name];

        // Loop through parent packages and merge their config in.
        while (isset($config['extends'])) {
            $extends = $config['extends'];
            unset($config['extends']);

            $parentConfig = $packagesConfig[$extends];

            if (isset($parentConfig['plugins'])) {
                if (isset($config['plugins'])) {
                    $config['plugins'] = array_merge_recursive($parentConfig['plugins'], $config['plugins']);
                } else {
                    $config['plugins'] = $parentConfig['plugins'];
                }
            }

            if (isset($parentConfig['extends'])) {
                $config['extends'] = $parentConfig['extends'];
            }

        }

        array_unshift($config['plugins'], 'Meta');

        return $config;
    }

    public static function getPackageList()
    {
        $config = self::getConfig();
        $packages = array_keys($config);

        if (($key = array_search('Core', $packages)) !== false) {
            unset($packages[$key]);
        }

        return $packages;
    }

    public static function getConfig()
    {
        if (!isset(static::$json)) {
            $resources = new Resources();
            $configPath = $resources->getPath('config');
            static::$json = json_decode(file_get_contents($configPath . 'packages.json'), true);
        }

        return static::$json;
    }

}
