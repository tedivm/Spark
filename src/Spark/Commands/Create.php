<?php

namespace Spark\Commands;

use Spark\Package;
use Spark\Resources;
use Spark\Builder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Base
{
    protected $description = 'Description';
    protected $help = <<<EOT
EOT;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $type = strtolower($input->getArgument('type'));

        if ($input->getOption('dir')) {
            $dir = $input->getOption('dir');
        } else {
            $dir = getcwd() . '/' . $name . '/';
        }

        if ($input->getOption('vendor')) {
            $vendor = $input->getOption('vendor');
        } else {
            $vendor = 'VENDOR';
        }

        if ($input->getOption('author')) {
            $author = $input->getOption('author');
        } elseif($vendor != 'VENDOR') {
            $author = $vendor;
        } else {
            $author = 'author';
        }

        $tags = array(
            'name' => $name,
            'vendor' => $vendor,
            'author' => $author
        );

        $package = new Package($type);
        $templateFiles = $package->getTemplateFiles($input);
        $templateSources = $package->getTemplateSources();
        $tags = $package->setTags($tags, $input);

        $builder = new Builder($dir);
        $builder->setSources($templateSources, $templateFiles['files'], $templateFiles['directories']);
        $builder->build($tags);

        $output->writeln($name . ' has been created using the ' . $type . ' package.');
    }

    protected function getPlugins($package)
    {
        $resources = new Resources();
        $configPath = $resources->getPath('config');

        $packages = json_decode(file_get_contents($configPath . 'packages.json'), true);

        if (!isset($packages[$package])) {
            throw new \RuntimeException('Not a supported type.');
        }

        return $packages[$package];
    }

}
