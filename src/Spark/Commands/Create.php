<?php

namespace Spark\Commands;

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

        $plugins = $this->getPlugins($type);

        $resources = new Resources();
        $templatePath = $resources->getPath('templates');

        $tags = array(
            'name' => $name,
            'vendor' => $vendor
        );

        $builder = new Builder($plugins, $templatePath);
        $builder->build($dir, $tags);

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

        return $packages[$type];
    }

}
