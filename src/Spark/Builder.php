<?php

namespace Spark;

use Symfony\Component\Yaml\Yaml;

class Builder
{
    protected $outputPath;

    protected $files;
    protected $directories;
    protected $sources;
    protected $permissions;

    protected $config;

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

    public function setConfig($config)
    {
        $this->config = $config;
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

        $twigChainLoader = new \Twig_Loader_Chain(array(
            new \Twig_Loader_Array($this->makeConfigTemplates()),
            new \Twig_Loader_Filesystem(array_reverse($this->sources)),
        ));

        $twigEnvironment = new \Twig_Environment($twigChainLoader);

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

    protected function makeConfigTemplates()
    {
        $json_options = 0;
        if (defined('JSON_PRETTY_PRINT')) {
            $json_options = $json_options | JSON_PRETTY_PRINT;
        }

        if (defined('JSON_UNESCAPED_SLASHES')) {
            $json_options = $json_options | JSON_UNESCAPED_SLASHES;
        }

        $templates = array();
        foreach ($this->config as $file => $rawContents) {

            $ext = pathinfo($file, PATHINFO_EXTENSION);

            if ($ext == 'dist') {
                $fileSubName = substr($file, 0, strlen($file) - 5);
                $ext = pathinfo($fileSubName, PATHINFO_EXTENSION);
            }

            switch ($ext) {
                case 'yml':
                    $contents = $this->yamlPretty(Yaml::dump($rawContents, 3));
                    break;

                case 'json':
                    $contents = json_encode($rawContents, $json_options);
                    break;

                default:
                    if (is_scalar($rawContents)) {
                        $contents = $rawContents;
                    } else {
                        throw new \RuntimeException('Unable to identify config file parser for ' . $file);
                    }
            }

            $templates[$file] = $contents;
        }

        return $templates;
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

    protected function yamlPretty($yaml)
    {
        $yamlLines = explode("\n", $yaml);
        $hasIndents = false;
        foreach ($yamlLines as $line) {
            if (preg_match('/^(\s+)/',$line,$matches) !== 0) {
                $hasIndents = true;
                break;
            }
        }

        if (!$hasIndents) {
            return $yaml;
        }

        $output = '';
        $previousSpaceCount = -1;
        foreach ($yamlLines as $line) {

            if (preg_match('/^(\s+)/',$line,$matches) !== 0) {
                $currentSpaceCount = strlen($matches[1]);
            } else {
                $currentSpaceCount = 0;
            }

            if ($currentSpaceCount < $previousSpaceCount || ($previousSpaceCount == 0 && $currentSpaceCount == 0)) {
                $output .= "\n";
            }
            $output .= $line . "\n";
            $previousSpaceCount = $currentSpaceCount;
        }

        return rtrim($output);

    }

}
