<?php

namespace Spark\Commands;

use Symfony\Component\Console\Command\Command;

abstract class Base extends Command {

    protected $name;
    protected $namespace;
    protected $description;
    protected $help;
    protected $options = array();
    protected $arguments = array();

    protected $metaDoc;

    protected function configure()
    {
        $class = get_class($this);
        $classname = substr($class, strrpos($class, '\\') + 1);

        $metaPath = __DIR__ . '/' . $classname . '.json';
        if(file_exists($metaPath)) {
            $this->metaDoc = json_decode($metaPath);
        }

        $commandName = isset($this->namespace) ? $this->namespace . ':' : '';
        $commandName .= isset($this->name) ? $this->name : strtolower($classname);

        $this->setName($commandName);
        $this->setDescription($this->description);

        $this->setName($this->getName())
            ->setDescription($this->getDescription())
            ->setHelp($this->help);


        foreach($this->getArguments() as $argument) {
            $this->addArgument(
                $argument[0], // name
                isset($argument[1]) ? $argument[1] : null, // mode
                isset($argument[2]) ? $argument[2] : '', // description
                isset($argument[3]) ? $argument[3] : null // default
            );
        }

        foreach($this->getOptions() as $option) {
            $this->addOption(
                $option[0], // name
                isset($option[1]) ? $option[1] : null, // shortcut
                isset($option[2]) ? $option[2] : null, // mode
                isset($option[3]) ? $option[3] : '', // description
                isset($option[4]) ? $option[4] : null // default
            );
        }
    }

    protected function getArguments()
    {
        $arguments = $this->arguments;

        if(is_array($this->metaDoc) && isset($this->metaDoc['arguments'])) {
            foreach($this->metaDoc['arguments'] as $metaArgument) {
                $arguments[] = array(
                    $metaArgument['name'], // name
                    isset($metaArgument['mode']) ? $metaArgument['mode'] : null, // mode
                    isset($metaArgument['description']) ? $metaArgument['description'] : '', // description
                    isset($metaArgument['default']) ? $metaArgument['default'] : null // default
                );
            }
        }

        return $arguments;
    }

    protected function getOptions()
    {
        $options = $this->options;

        if(is_array($this->metaDoc) && isset($this->metaDoc['options'])) {
            foreach($this->metaDoc['options'] as $metaOption) {
                $options[] = array(
                    $metaOption['name'], // name
                    isset($metaOption['shortcut']) ? $metaOption['shortcut'] : null, // mode
                    isset($metaOption['mode']) ? $metaOption['mode'] : null, // mode
                    isset($metaOption['description']) ? $metaOption['description'] : '', // description
                    isset($metaOption['default']) ? $metaOption['default'] : null // default
                );
            }
        }

        return $options;
    }

}