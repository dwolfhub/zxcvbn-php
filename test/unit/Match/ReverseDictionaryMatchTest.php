<?php

namespace unit\Match;

use Zxcvbn\Match\ReverseDictionaryMatch;

class ReverseDictionaryMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReverseDictionaryMatch
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new ReverseDictionaryMatch();
    }

    public function testIsTesting()
    {
        $this->markTestIncomplete();
        // @todo remove
        $this->assertFalse(true);
    }
}
