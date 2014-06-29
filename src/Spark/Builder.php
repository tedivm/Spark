<?php

namespace Spark;

class Builder
{
    protected $outputPath;

    protected $files;
    protected $directories;
    protected $sources;
    protected $permissions;

    public function __construct($outputPath)
    {
        $this->outputPath = $outputPath;
    }

    public function setSources($sources, $files, $directories, $permissions)
    {
        $this->files = $files;
        $this->directories = $directories;
        $this->sources = $sources;
        $this->permissions = $permissions;
    }

    public function build($tags)
    {
        $this->makeDirectories($tags);
        $this->makeFiles($tags);
        $this->setPermissions($tags);
    }

    protected function makeDirectories($tags)
    {
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath);
        }

        $fsTags = $this->getFilesystemReplacements($tags);

        foreach ($this->directories as $directory) {
            $newDir = $this->outputPath . str_replace($fsTags[0], $fsTags[1], $directory);
            if (!is_dir($newDir)) {
                mkdir($newDir);
            }
        }
    }

    protected function makeFiles($tags)
    {
        $fsTags = $this->getFilesystemReplacements($tags);

        $twigFilesystem = new \Twig_Loader_Filesystem(array_reverse($this->sources));
        $twigEnvironment = new \Twig_Environment($twigFilesystem);

        foreach ($this->files as $file) {

            $newFile = $this->outputPath . str_replace($fsTags[0], $fsTags[1], $file);

            if (!file_exists($newFile)) {
                $contents = $twigEnvironment->render($file, $tags);

                if (file_put_contents($newFile, $contents) === false) {
                    throw new \RuntimeException('Unable to create file: ' . $newFile);
                }
            }
        }
    }

    protected function setPermissions($tags)
    {
        $fsTags = $this->getFilesystemReplacements($tags);

        foreach ($this->permissions as $file => $permission) {
            $newFile = $this->outputPath . str_replace($fsTags[0], $fsTags[1], $file);
            chmod($newFile, $permission);
        }
    }


    protected function getFilesystemReplacements($tags)
    {
        $tagNames = array();
        $tagValues = array();
        foreach ($tags as $name => $value) {
            $tagNames[] = '__' . strtoupper($name) . '__';
            $tagValues[] = $value;
        }

        return array($tagNames, $tagValues);
    }

}
