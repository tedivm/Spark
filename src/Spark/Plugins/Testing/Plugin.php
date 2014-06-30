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
}
