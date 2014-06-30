<?php

namespace Spark\Plugins\General;

use Spark\Plugins\Core\Plugin as Base;
use Symfony\Component\Console\Input\InputInterface;

class Plugin extends Base
{
    protected $tags = array(
        'source_dir' => 'src',
    );

    public function getConfig($config, $tags, InputInterface $input)
    {
        $authorArray = array();

        if($author = $input->getOption('author')) {
            $authorArray['name'] = $author;
        }

        if($email = $input->getOption('email')) {
            $authorArray['email'] = $email;
        }

        if(count($authorArray) > 0) {
            $this->addToConfig('composer.json', array('authors' => array($authorArray)));
        }

        if(isset($tags['tagline'])) {
            $this->config['composer.json']['description'] = $tags['tagline'];
        }

        return parent::getConfig($config, $tags, $input);
    }

}
