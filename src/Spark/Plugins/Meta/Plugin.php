<?php

namespace Spark\Plugins\Meta;

use Spark\Plugins\Core\Plugin as Base;
use Symfony\Component\Console\Input\InputInterface;


class Plugin extends Base
{
    public function setTags(&$tags, InputInterface $input)
    {
        if ($input->getOption('vendor')) {
            $vendor = $input->getOption('vendor');
        } else {
            $vendor = 'VENDOR';
        }

        if ($input->getOption('author')) {
            $author = $input->getOption('author');
        } elseif ($vendor != 'VENDOR') {
            $author = $vendor;
        } else {
            $author = 'author';
        }

        if ($input->getOption('email')) {
            $tags['email'] = $input->getOption('email');
        }

        if ($input->getOption('tagline')) {
            $tags['tagline'] = $input->getOption('tagline');
        }

        $tags['vendor'] = $vendor;
        $tags['author'] = $author;
    }
}
