<?php

namespace Spark\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\RuntimeException;

class Config extends Base
{
    protected $description = 'Define default configuration settings for new projects.';
    protected $help = <<<EOT
The <info>%command.name%</info> command sets default options for new projects:

  <info>php %command.full_name% -l mit</info>
  <info>php %command.full_name% -a Robert</info>
  <info>php %command.full_name% -u tedivm</info>

You can reset back to the built in defaults with the <comment>--clear</comment> option:

  <info>php %command.full_name% --clear</info>
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
            if (file_exists($path)) {
                if (!is_writable($path)) {
                    throw new RuntimeException('Need write permission to erase file ' . $path);
                }

                unlink($path);
                $output->writeln('Configuration deleted from ' . $path);
            } else {
                $output->writeln('Configuration was not present at ' . $path);
            }

            return;
        }

        $rawOptions = $input->getOptions();
        $options = array();
        foreach ($rawOptions as $name => $value) {
            if (!in_array($name, $this->ignoreOptions) && !is_null($value) && $value !== '') {
                $options[$name] = $value;
            }
        }

        $jsonSettings = 0;
        if (defined('JSON_PRETTY_PRINT')) {
            $jsonSettings = $jsonSettings | JSON_PRETTY_PRINT;
        }

        if (file_put_contents($path, json_encode($options, $jsonSettings))) {
            $output->writeln('Configuration saved to ' . $path);
        } else {
            $output->writeln('Unable to save configuration to ' . $path);
        }
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
