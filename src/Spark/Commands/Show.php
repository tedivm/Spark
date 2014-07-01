<?php

namespace Spark\Commands;

use Spark\PackageInfo;
use Spark\PluginManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class Show extends Base
{
    protected $description = 'Get information about available Packages and Plugins.';
    protected $help = <<<EOT
EOT;

    protected $types = array('Package', 'Plugin');

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = strtolower($input->getArgument('type'));

        if (substr($type, -1) == 's') {
            $type = substr($type, 0, strlen($type) - 1);
        }

        $type = ucfirst(strtolower($type));

        if (!in_array($type, $this->types)) {
            $this->outputCommandTypes($input, $output);

            return;
        }

        $function = 'output';
        $function .= ucfirst(strtolower($type));
        $function .= is_string($input->getArgument('name')) ? 'Summary' : 'Listing';

        if (method_exists($this, $function)) {
            $this->$function($input, $output);
        }

    }

    protected function outputCommandTypes(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('show ' . implode(', ', $this->types));
    }

    protected function outputPackageListing(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);

        $table->setHeaders(array('Package', 'Description'));

        $packageList = PackageInfo::getPackageList();
        sort($packageList);

        foreach ($packageList as $package) {
            $packageInfo = new PackageInfo($package);

            $row = array(
                $packageInfo->getName(),
                ($description = $packageInfo->getDescription()) ? $description : null
            );

            $table->addRow($row);
        }

        $table->render();
    }

    protected function outputPackageSummary(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $packageInfo = new PackageInfo($name);

        $output->writeln($packageInfo->getName());

        if ($description = $packageInfo->getDescription()) {
            $output->writeln($description);
        }

        $output->writeln('Package contains the following plugins:');
        $plugins = $packageInfo->getPlugins();
        $this->outputPluginListing($input, $output, $plugins);

    }

    protected function outputPluginListing(InputInterface $input, OutputInterface $output, $list = null)
    {
        if (is_null($list)) {
            $list = PluginManager::getPluginList();
        }

        $table = new Table($output);
        $table->setHeaders(array('Plugin', 'Description'));

        foreach ($list as $plugin) {

            $plugin = PluginManager::getPluginObject($plugin);
            $config = $plugin->getDescription();

            $row = array(
                $config['name'],
                isset($config['description']) ? $config['description'] : null
            );
            $table->addRow($row);
        }

        $table->render();

    }

    protected function outputPluginSummary(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $plugin = PluginManager::getPluginObject($name);
        $config = $plugin->getDescription();
        $output->writeln($name);

        if (isset($config['description'])) {
            $output->writeln($config['description']);
        }
    }

}
