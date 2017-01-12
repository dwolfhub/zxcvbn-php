<?php

namespace unit\Match;

use Zxcvbn\Match\SpatialMatch;

class SpatialMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SpatialMatch
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new SpatialMatch();
    }

    public function testIsTesting()
    {
        $this->markTestIncomplete();
        // @todo remove
        $this->assertFalse(true);
    }
}
