<?php

namespace Spark;

use Symfony\Component\Finder\Finder;

class Builder
{

    protected $plugins;
    protected $pluginPath;

    protected $templateSourceDirectories = array();

    protected $outputDirectories = array();
    protected $outputFiles = array();

    public function __construct($plugins, $pluginsPath)
    {
        $this->plugins = $plugins;
        $this->pluginPath = $pluginsPath;
    }

    public function build($outputDirectory, $tags)
    {
        $this->getTemplateFiles($tags);
        $this->makeDirectories($outputDirectory);
        $this->makeFiles($outputDirectory, $tags);
    }

    protected function makeDirectories($base)
    {
        if (!is_dir($base)) {
            mkdir($base);
        }

        foreach ($this->outputDirectories as $directory) {
            $newDir = $base . $directory;
            if (!is_dir($newDir)) {
                mkdir($newDir);
            }
        }
    }

    protected function makeFiles($base, $tags)
    {
        $paths = $this->templateSourceDirectories;

        $twigFilesystem = new \Twig_Loader_Filesystem(array_reverse($paths));
        $twigEnvironment = new \Twig_Environment($twigFilesystem);

        foreach ($this->outputFiles as $file) {
            $newFile = $base . $file;

            if (!file_exists($newFile)) {
                $contents = $twigEnvironment->render($file, $tags);
                file_put_contents($newFile, $contents);
            }
        }

    }

    protected function getTemplateFiles($tags)
    {
        $plugins = $this->plugins;
        $pluginPath = $this->pluginPath;

        $tagName = array();
        $tagValue = array();
        foreach($tags as $name => $value) {
            $tagName[] = strtoupper($name);
            $tagValue[] = $value;
        }


        foreach ($plugins as $plugin) {
            $path = $pluginPath . $plugin;
            $this->templateSourceDirectories[] = $path;
            $pathLen = strlen($path);

            $finder = new Finder();
            $finder->in($path)->ignoreVCS(false)->notName('.gitkeep')->ignoreDotFiles(false);

            foreach ($finder as $file) {
                $longPath = $file->getRealpath();
                $processedPath = str_replace($tagName, $tagValue, $longPath);
                $shortPath = substr($processedPath, $pathLen + 1);
                if ($file->isDir()) {
                    $this->outputDirectories[] = $shortPath;
                } else {
                    $this->outputFiles[] = $shortPath;
                }
            }
        }
    }

}
