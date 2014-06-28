<?php

namespace Spark\Plugins\License;

use Spark\Plugins\Core\Plugin as Base;
use Symfony\Component\Console\Input\InputInterface;

class Plugin extends Base
{
    public function getTemplateFiles(InputInterface $input)
    {
        $license = $input->getOption('license');
        if ($license == false || $license == 'none') {
            return array();
        }

        return array('files' => array('LICENSE'));
    }


    public function setTags(&$tags, InputInterface $input)
    {
        $license = $input->getOption('license');
        if ($license !== false && $license !== 'none') {
            $tags['license'] = $license;
        }
    }
}
