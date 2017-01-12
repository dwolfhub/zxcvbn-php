<?php

namespace unit\Match;

use Zxcvbn\Match\RepeatMatch;

class RepeatMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RepeatMatch
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new RepeatMatch();
    }

    public function testIsTesting()
    {
        $this->markTestIncomplete();
        // @todo remove
        $this->assertFalse(true);
    }
}
