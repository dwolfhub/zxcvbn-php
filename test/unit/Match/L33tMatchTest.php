<?php

namespace unit\Match;

use Zxcvbn\Match\L33tMatch;

class L33tMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var L33tMatch
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new L33tMatch();
    }

    public function testIsTesting()
    {
        $this->markTestIncomplete();
        // @todo remove
        $this->assertFalse(true);
    }
}
