<?php

namespace Spark\Commands;

use Guzzle\Common\Exception\RuntimeException;
use Spark\Resources;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

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




        $output->writeln($name . ' ' . $type);

    }
}