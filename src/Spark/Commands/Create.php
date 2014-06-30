<?php

namespace Spark\Commands;

use Spark\Package;
use Spark\Resources;
use Spark\Builder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Base
{
    protected $description = 'Create new development project from specified package.';
    protected $help = <<<EOT
The <info>%command.name%</info> command creates new project:

  <info>php %command.full_name% ProjectName</info>

You can select different project types:

  <info>php %command.full_name% ProjectName general</info>
  <info>php %command.full_name% ProjectName cli</info>

You can specify which directory the project will be created with by using the <comment>--dir</comment> option:

  <info>php %command.full_name% ProjectName --dir=/path/to/new/Project</info>
  <info>php %command.full_name% ProjectName -d /path/to/new/Project</info>

It's also possible to change different template values:

  <info>php %command.full_name% ProjectName -l mit</info>
  <info>php %command.full_name% ProjectName -a Robert</info>
  <info>php %command.full_name% ProjectName -u tedivm</info>
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

        $tags = array(
            'name' => $name
        );

        $package = new Package($type);
        $templateFiles = $package->getTemplateFiles($input);
        $templateSources = $package->getTemplateSources();
        $templatePermissions = $package->getPermissions();
        $tags = $package->setTags($tags, $input);
        $config = $package->getConfig($input);

        $builder = new Builder($dir);
        $builder->setSources(
            $templateSources,
            $templateFiles['files'],
            $templateFiles['directories'],
            $templatePermissions
        );
        $builder->setConfig($config);
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
