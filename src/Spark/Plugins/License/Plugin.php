<?php

namespace Spark\Plugins\License;

use Spark\Plugins\Core\Plugin as Base;
use Symfony\Component\Console\Input\InputInterface;

class Plugin extends Base
{
    protected $licenseAliases = array(
        'agpl' => 'AGPL-3.0',

        'apache' => 'Apache-2.0',

        'artistic' => 'Artistic-2.0',

        'bsd2' => 'BSD-2-Clause',
        'bsd3' => 'BSD-3-Clause',

        'cc0' => 'cc0',

        'gpl' => 'GPL-2.0',
        'gpl3' => 'GPL-3.0',

        'isc' => 'isc',

        'lgpl' => 'LGPL-2.1',
        'lgpl3' => 'LGPL-3.0',

        'mit' => 'MIT',

        'mpl' => 'MPL-2.0',

        'unlicense' => 'UNLICENSE',

        'reserved' => 'All-Rights-Reserved',
        'proprietary' => 'All-Rights-Reserved'
    );

    public function getTemplateFiles(InputInterface $input)
    {
        $license = $input->getOption('license');
        if ($license == false || $license == 'none') {
            return array();
        }

        if ($license == 'unlicense') {
            return array('files' => array('UNLICENSE'));
        }

        return array('files' => array('LICENSE'));
    }

    public function setTags(&$tags, InputInterface $input)
    {
        $license = $input->getOption('license');
        if ($license !== false && $license !== 'none') {

            if (isset($this->licenseAliases[$license])) {
                $license = $this->licenseAliases[$license];
            }

            $tags['license'] = $license;
        }
    }

    public function getConfig($config, $tags, InputInterface $input)
    {
        $config = parent::getConfig($config, $tags, $input);

        $license = $tags['license'];

        if (isset($tags['license']) && $tags['license'] !== 'none') {
            if (isset($this->licenseAliases[$license])) {
                $license = $this->licenseAliases[$tags['license']];
            } else {
                $license = $tags['license'];
            }
            $config['composer.json']['license'] = $license;
        }

        return $config;
    }

}
