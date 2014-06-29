<?php

namespace Spark\Plugins\Core;

use Spark\Resources;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

abstract class Plugin
{
    protected $name;
    protected $directory;

    protected $configExtensions = array('yml', 'json');
    protected $templateFiles = array();

    protected $permissions = array();
    protected $config = array();

    public function __construct()
    {
        $className = get_class($this);
        $classNameParts = explode('\\', $className);
        $classNamePartCount = count($classNameParts);

        $this->name = $classNameParts[$classNamePartCount - 2];
        $resources = new Resources();
        $this->directory = $resources->getPath('plugins') . $this->name . '/';

        $this->loadTemplateFiles();
    }

    public function getCommandOptions($command)
    {
        $path = $this->getPath('Commands');
        if ($path === false) {
            return array();
        }

        $path .= $command . '.json';

        if (!file_exists($path)) {
            return array();
        }

        $json = json_decode(file_get_contents($path), true);

        if (isset($json['options'])) {
            return $json['options'];
        } else {
            return array();
        }
    }

    public function getPath($type = null)
    {
        $path = $this->directory;

        if (isset($type)) {
            $path .= $type . '/';
        }

        return file_exists($path) ? $path : false;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function getTemplateFiles(InputInterface $input)
    {
        return $this->templateFiles;
    }

    public function loadTemplateFiles()
    {
        $path = $this->getPath('Templates');
        if ($path === false) {
            return array();
        }

        $pathLen = strlen($path);

        $finder = new Finder();
        $finder->in($path)
            ->notName('.gitkeep')
            ->ignoreVCS(false)
            ->ignoreDotFiles(false);

        $output = array();

        /** @var $file \SplFileInfo */
        foreach ($finder as $file) {

            $longPath = $file->getPath() . '/' . $file->getFilename();
            $shortPath = substr($longPath, $pathLen);

            if ($file->isDir()) {
                $output['directories'][] = $shortPath;
            } else {

                $extension = $file->getExtension();

                // If it's a "dist" file find the real extension.
                if ($extension == 'dist') {
                    $fileName = $file->getFilename();
                    $fileSubName = substr($fileName, 0, strlen($fileName) - 5);
                    $extension = pathinfo($fileSubName, PATHINFO_EXTENSION);
                }

                switch ($extension) {
                    case 'json':
                        $this->config[$shortPath] = json_decode(file_get_contents($longPath), true);
                        break;

                    case 'yml':
                        $this->config[$shortPath] = Yaml::parse(file_get_contents($longPath));
                        break;

                }

                $output['files'][] = $shortPath;
            }
        }

        $this->templateFiles = $output;
    }

    public function setTags(&$tags, InputInterface $input)
    {

    }

    public function setConfig(&$config, InputInterface $input)
    {
        $config = array_merge_recursive($config, $this->config);
    }

}
