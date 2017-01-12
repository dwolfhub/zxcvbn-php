<?php

namespace unit\Match;

use Zxcvbn\Match\MatchFactory;

class MatchFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MatchFactory
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new MatchFactory();
    }

    public function testIsTesting()
    {
        $this->markTestIncomplete();
        // @todo remove
        $this->assertFalse(true);
    }
}
