<?php

namespace unit\Match;

use Zxcvbn\Match\DictionaryMatch;

class DictionaryMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DictionaryMatch
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new DictionaryMatch();
    }

    public function testIsTesting()
    {
        $this->markTestIncomplete();
        // @todo remove
        $this->assertFalse(true);
    }
}
