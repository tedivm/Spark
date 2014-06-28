<?php

namespace Spark\Plugins\Core;

use Spark\Resources;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;

abstract class Plugin
{
    protected $name;
    protected $directory;

    public function __construct()
    {
        $className = get_class($this);
        $classNameParts = explode('\\', $className);
        $classNamePartCount = count($classNameParts);

        $this->name = $classNameParts[$classNamePartCount - 2];
        $resources = new Resources();
        $this->directory = $resources->getPath('plugins') . $this->name . '/';
    }

    public function getPath($type = null)
    {
        $path = $this->directory;

        if (isset($type)) {
            $path .= $type . '/';
        }

        return file_exists($path) ? $path : false;
    }

    public function getTemplateFiles()
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
        foreach ($finder as $file) {

            $longPath = $file->getPath() . '/' . $file->getFilename();
            $shortPath = substr($longPath, $pathLen);

            if ($file->isDir()) {
                $output['directories'][] = $shortPath;
            } else {
                $output['files'][] = $shortPath;
            }
        }
//var_dump($output);
        return $output;
    }

    public function setTags(&$tags, InputInterface $input)
    {

    }

}
