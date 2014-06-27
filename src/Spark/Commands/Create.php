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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input = $this->getInput($input);
        $name = $input['name'];
        $type = $input['type'];
        $dir = $input['dir'];
        $plugins = $input['plugins'];
        $configPath = $input['configPath'];
        $vendor = $input['vendor'];
        $templatePath = $input['templatePath'];
        $templateFiles = $this->getTemplateFiles($name, $plugins, $templatePath);

        $tags = array(
            'name' => $name,
            'vendor' => $vendor
        );

        $this->makeDirectories($dir, $templateFiles['directories']);
        $this->makeFiles($dir, $templateFiles['paths'], $templateFiles['files'], $tags);

        $output->writeln($name . ' has been created using the ' . $type . ' package.');
    }

    protected function getInput(InputInterface $input)
    {
        $name = $input->getArgument('name');
        $type = $input->getArgument('type');

        if($input->getOption('dir')) {
            $dir = $input->getOption('dir');
        }else{
            $dir = getcwd() . '/' . $name . '/';
        }

        if($input->getOption('vendor')) {
            $vendor = $input->getOption('vendor');
        }else{
            $vendor = 'VENDOR';
        }

        $resources = new Resources();
        $configPath = $resources->getPath('config');
        $templatePath = $resources->getPath('templates');

        $packages = json_decode(file_get_contents($configPath . 'packages.json'), true);

        if(!isset($packages[$type])) {
            throw new \RuntimeException('Not a supported type.');
        }

        $options['name'] = $name;
        $options['type'] = $type;
        $options['dir'] = $dir;
        $options['plugins'] = $packages[$type];
        $options['configPath'] = $configPath;
        $options['templatePath'] = $templatePath;
        $options['vendor'] = $vendor;
        return $options;
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
        $twigFilesystem = new \Twig_Loader_Filesystem(array_reverse($paths));
        $twigEnvironment = new \Twig_Environment($twigFilesystem);

        foreach($files as $file) {
            $newFile = $base . $file;

            if(!file_exists($newFile)) {
                $contents = $twigEnvironment->render($file, $tags);
                file_put_contents($newFile, $contents);
            }
        }

    }

    protected function getTemplateFiles($name, $plugins, $templatePath)
    {
        $paths = array();
        $files = array();
        $directories = array();

        foreach($plugins as $plugin) {
            $path = $templatePath . $plugin;
            $paths[] = $path;
            $pathLen = strlen($path);

            $finder = new Finder();
            $finder->in($path)->ignoreVCS(false)->notName('.gitkeep')->ignoreDotFiles(false);

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
        return array('directories' => $directories, 'files' => $files, 'paths' => $paths);
    }


}