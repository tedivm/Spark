<?php

namespace Spark\Commands;

use Spark\PluginManager;
use Spark\UserValues;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class Base extends Command
{
    protected $classname;
    protected $namespace;
    protected $description;
    protected $help;
    protected $options = array();
    protected $arguments = array();

    protected $metaDoc;

    protected function configure()
    {
        $class = get_class($this);
        $this->classname = substr($class, strrpos($class, '\\') + 1);

        $metaPath = __DIR__ . '/' . $this->classname . '.json';
        if (file_exists($metaPath)) {
            $this->metaDoc = json_decode(file_get_contents($metaPath), true);
        }

        $commandName = isset($this->namespace) ? $this->namespace . ':' : '';
        $commandName .= strtolower($this->classname);

        $this->setName($commandName);
        $this->setDescription($this->description);

        $this->setName($this->getName())
            ->setDescription($this->getDescription())
            ->setHelp($this->help);

        foreach ($this->getArgumentConfigs() as $argument) {
            $this->addArgument(
                $argument[0], // name
                isset($argument[1]) ? $argument[1] : null, // mode
                isset($argument[2]) ? $argument[2] : '', // description
                isset($argument[3]) ? $argument[3] : null // default
            );
        }

        $options = $this->getOptionConfigs();
        usort($options, function ($a, $b) {
                return strcmp($a[0], $b[0]);
            }
        );

        $userValues = new UserValues();
        foreach ($options as $option) {

            $userOverride = $userValues->getValue($option[0]);
            if (!is_null($userOverride)) {
                $option[4] = $userOverride;
            }

            $this->addOption(
                $option[0], // name
                isset($option[1]) ? $option[1] : null, // shortcut
                isset($option[2]) ? $option[2] : null, // mode
                isset($option[3]) ? $option[3] : '', // description
                isset($option[4]) ? $option[4] : null // default
            );
        }
    }

    public function getArgumentConfigs()
    {
        $arguments = $this->arguments;

        if (is_array($this->metaDoc) && isset($this->metaDoc['arguments'])) {
            foreach ($this->metaDoc['arguments'] as $metaArgument) {

                if (isset($metaArgument['required']) && $metaArgument['required'] === true) {
                    $mode = InputArgument::REQUIRED;
                } else {
                    $mode = InputArgument::OPTIONAL;
                }

                if (isset($metaArgument['array']) && $metaArgument['array'] === true) {
                    $mode = $mode | InputArgument::IS_ARRAY;
                }

                $arguments[] = array(
                    $metaArgument['name'], // name
                    $mode, // mode
                    isset($metaArgument['description']) ? $metaArgument['description'] : '', // description
                    isset($metaArgument['default']) ? $metaArgument['default'] : null // default
                );
            }
        }

        return $arguments;
    }

    public function getOptionConfigs()
    {
        $options = $this->options;

        $plugins = PluginManager::getPluginList();

        if (is_array($this->metaDoc) && isset($this->metaDoc['options'])) {
            $rawOptions = $this->metaDoc['options'];
        } else {
            $rawOptions = array();
        }

        foreach ($plugins as $plugin) {
            $pluginItem = PluginManager::getPluginObject($plugin);
            $pluginOptions = $pluginItem->getCommandOptions($this->classname);
            $rawOptions = array_merge_recursive($rawOptions, $pluginOptions);
        }

        foreach ($rawOptions as $metaOption) {
            // See if it allows input
            if (isset($metaOption['input']) && $metaOption['input'] === true) {

                // Defaults to optional if not set.
                if (isset($metaOption['required']) && $metaOption['required'] === true) {
                    $mode = InputOption::VALUE_REQUIRED;
                } else {
                    $mode = InputOption::VALUE_OPTIONAL;
                }

                // Defaults to single value
                if (isset($metaOption['array']) && $metaOption['array'] === true) {
                    $mode = $mode | InputOption::VALUE_IS_ARRAY;
                }

            } else {
                $mode = InputOption::VALUE_NONE;
            }

            $options[] = array(
                $metaOption['name'], // name
                isset($metaOption['shortcut']) ? $metaOption['shortcut'] : null, // mode
                $mode, // mode
                isset($metaOption['description']) ? $metaOption['description'] : '', // description
                isset($metaOption['default']) ? $metaOption['default'] : null // default
            );
        }

        return $options;
    }

}
