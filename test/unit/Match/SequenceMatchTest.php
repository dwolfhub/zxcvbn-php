<?php

namespace unit\Match;

use Zxcvbn\Match\SequenceMatch;

class SequenceMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SequenceMatch
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new SequenceMatch();
    }

    public function testIsTesting()
    {
        $this->markTestIncomplete();
        // @todo remove
        $this->assertFalse(true);
    }
}
