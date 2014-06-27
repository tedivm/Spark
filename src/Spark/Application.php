<?php

namespace Spark;

use Symfony\Component\Finder\Finder;

class Application extends \Symfony\Component\Console\Application
{

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $finder = new Finder();
        $finder->files()->name('*.php')->depth('0')->in(__DIR__ . '/Commands/')->sortByName();

        foreach ($finder as $file) {

            $fileName = $file->getFilename();
            $class = substr($fileName, 0, strlen($fileName) - 4);
            $fullClassName = '\\Spark\\Commands\\' . $class;

            $reflectionClass = new \ReflectionClass($fullClassName);
            if ($reflectionClass->IsInstantiable()) {
                $commands[] = new $fullClassName();
            }
        }

        return $commands;
    }

}
