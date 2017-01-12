<?php

namespace unit\Match;

use Zxcvbn\Match\RegexMatch;

class RegexMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegexMatch
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new RegexMatch();
    }

    public function testIsTesting()
    {
        $this->markTestIncomplete();
        // @todo remove
        $this->assertFalse(true);
    }
}
