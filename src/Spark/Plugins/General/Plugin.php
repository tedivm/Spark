<?php

namespace Spark\Plugins\General;

use Spark\Plugins\Core\Plugin as Base;

class Plugin extends Base
{
    protected $permissions = array(
        'tests//travis/php_setup.sh' => 0755,
        'tests//runTests.sh' => 0755,
    );
}
