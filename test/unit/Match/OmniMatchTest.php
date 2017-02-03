<?php

namespace unit\Match;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\OmniMatch;

class OmniMatchTest extends TestCase
{
    /**
     * @var OmniMatch
     */
    protected $omniMatch;

    public function setUp()
    {
        $this->omniMatch = new OmniMatch();
    }

    public function testReturnsDateMatches()
    {
        $this->markTestIncomplete();
        $this->omniMatch->setPassword('2005');
        $this->assertTrue([], $this->omniMatch->getMatches());
    }

    public function testReturnsDictionaryMatches()
    {
        
    }

    public function testReturnsL33tMatches()
    {
        
    }

    public function testReturnsRegexMatches()
    {

    }

    public function testReturnsRepeatMatches()
    {

    }

    public function testReturnsReverseDictionaryMatches()
    {

    }

    public function testReturnsSequenceMatches()
    {

    }

    public function testReturnsSpatialMatches()
    {

    }
}
