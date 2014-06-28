<?php

/*
 * This file is part of the Stash package.
 *
 * (c) Robert Hafner <tedivm@tedivm.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spark\Test;

use Spark\Resources;

class ResourcesTest extends \PHPUnit_Framework_TestCase
{
    protected $types = array('plugins', 'config');

    public function testGetPath()
    {
        $resources = new Resources();

        foreach ($this->types as $type) {

            $path = $resources->getPath($type);
            $this->assertTrue(file_exists($path), 'Returns valid path.');
            $this->assertTrue(is_dir($path), 'Returns directory.');
        }

    }

}
