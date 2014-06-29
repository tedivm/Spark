<?php

namespace Spark\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Config extends Base
{
    protected $description = 'Description';
    protected $help = <<<EOT
EOT;

    protected $ignoreOptions = array(
        'clear',
        'dir',
        'help',
        'quiet',
        'verbose',
        'version',
        'ansi',
        'no-ansi',
        'no-interaction',
    );

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $_SERVER['HOME'] . '/.spark.json';

        if ($input->getOption('clear')) {
            unlink($path);
            $output->writeln('Configuration deleted from ' . $path);

            return;
        }

        $rawOptions = $input->getOptions();
        $options = array();
        foreach ($rawOptions as $name => $value) {
            if (!in_array($name, $this->ignoreOptions) && !is_null($value) && $value !== '') {
                $options[$name] = $value;
            }
        }

        file_put_contents($path, json_encode($options));

        $output->writeln('Configuration saved to ' . $path);
    }

    public function getOptionConfigs()
    {
        $optionConfigs = parent::getOptionConfigs();
        $createCommand = new Create();
        $createOptionsConfigsUnfiltered = $createCommand->getOptionConfigs();

        $createOptionsConfigs = array();
        foreach ($createOptionsConfigsUnfiltered as $options) {
            if ($options[0] != 'dir') {
                $createOptionsConfigs[] = $options;
            }
        }

        return array_merge_recursive($createOptionsConfigs, $optionConfigs);
    }

}
