<?php

namespace Spark\Plugins\Testing;

use Spark\Plugins\Core\Plugin as Base;

class Plugin extends Base
{
    protected $permissions = array(
        'tests//travis/php_setup.sh' => 0755,
        'tests//runTests.sh' => 0755,
    );

    protected $tags = array(
        'test_dir' => 'tests',
    );

    protected $config = array(
        'composer.json' =>
            array('require-dev' =>
                array(
                    "phpunit/phpunit" => "4.0.*",
                    "fabpot/php-cs-fixer"=> "0.4.0",
                    "satooshi/php-coveralls"=> "dev-master"
                )
            )
    );

}
