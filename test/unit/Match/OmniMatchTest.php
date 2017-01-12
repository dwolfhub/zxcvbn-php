<?php

namespace unit\Match;

use Zxcvbn\Match\OmniMatch;

class OmniMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OmniMatch
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new OmniMatch();
    }

    public function testIsTesting()
    {
        $this->markTestIncomplete();
        // @todo remove
        $this->assertFalse(true);
    }
}
