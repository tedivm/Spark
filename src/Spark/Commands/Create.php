<?php

namespace Spark\Commands;

use Spark\Resources;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Finder\Finder;


class Create extends Base {

    protected $description = 'Description';
    protected $help = <<<EOT
EOT;

    protected $types = array('general');

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $type = $input->getArgument('type');

        if(!in_array($type, $this->types)) {
            throw new \RuntimeException('Not a supported type.');
        }

        if($input->getOption('dir')) {
            $dir = $input->getOption('dir');
        }else{
            $dir = getcwd() . '/' . $name . '/';
        }

        $resources = new Resources();
        $configPath = $resources->getPath('config');
        $templatePath = $resources->getPath('templates');

        $packages = json_decode(file_get_contents($configPath . 'packages.json'), true);

        $paths = array();
        $files = array();
        $directories = array();
        $plugins = $packages[$type];

        foreach($plugins as $plugin) {
            $path = $templatePath . $plugin;
            $paths[] = $path;
            $pathLen = strlen($path);

            $finder = new Finder();
            $finder->in($path);

            foreach ($finder as $file) {
                $longPath = $file->getRealpath();
                $processedPath = str_replace('PROJECTNAME', $name, $longPath);
                $shortPath = substr($processedPath, $pathLen + 1);
                if($file->isDir()) {
                    $directories[] = $shortPath;
                } else {
                    $files[] = $shortPath;
                }
            }
        }


        $tags = array('name' => $name);

        $this->makeDirectories($dir, $directories);
        $this->makeFiles($dir, $paths, $files, $tags);



        $output->writeln($name . ' has been created using the ' . $type . ' package.');
    }

    protected function makeDirectories($base, $directories)
    {
        if(!is_dir($base)) {
            mkdir($base);
        }

        foreach($directories as $directory) {
            $newDir = $base . $directory;
            if(!is_dir($newDir)) {
                mkdir($newDir);
            }
        }
    }

    protected function makeFiles($base, $paths, $files, $tags)
    {
        $twigFilesystem = new \Twig_Loader_Filesystem($paths);
        $twigEnvironment = new \Twig_Environment($twigFilesystem);

        foreach($files as $file) {
            $newFile = $base . $file;

            if(!file_exists($newFile)) {
                $contents = $twigEnvironment->render($file, $tags);
                file_put_contents($newFile, $contents);
            }
        }

    }




}