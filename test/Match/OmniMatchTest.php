<?php

namespace test\Match;

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
        $this->omniMatch->setPassword('010186');
        $matches = $this->omniMatch->getMatches();
        $foundDateMatch = false;
        foreach ($matches as $match) {
            if ($match['pattern'] === 'date') {
                $foundDateMatch = true;
            }
        }
        $this->assertTrue($foundDateMatch);
    }

    public function testReturnsDictionaryMatches()
    {
        $this->omniMatch->setPassword('password');
        $matches = $this->omniMatch->getMatches();
        $foundDictionaryMatch = false;
        foreach ($matches as $match) {
            if ($match['pattern'] === 'dictionary') {
                $foundDictionaryMatch = true;
            }
        }
        $this->assertTrue($foundDictionaryMatch);
    }

    public function testReturnsL33tMatches()
    {
        $this->omniMatch->setPassword('p4ssw0rd');
        $matches = $this->omniMatch->getMatches();
        $foundL33tMatch = false;
        foreach ($matches as $match) {
            if ($match['pattern'] === 'dictionary' and !empty($match['l33t'])) {
                $foundL33tMatch = true;
            }
        }
        $this->assertTrue($foundL33tMatch);
    }

    public function testReturnsRegexMatches()
    {
        $this->omniMatch->setPassword('2017');
        $matches = $this->omniMatch->getMatches();
        $foundRegexMatch = false;
        foreach ($matches as $match) {
            if ($match['pattern'] === 'regex') {
                $foundRegexMatch = true;
            }
        }
        $this->assertTrue($foundRegexMatch);
    }

    public function testReturnsRepeatMatches()
    {
        $this->omniMatch->setPassword('testtest');
        $matches = $this->omniMatch->getMatches();
        $foundRepeatMatch = false;
        foreach ($matches as $match) {
            if ($match['pattern'] === 'repeat') {
                $foundRepeatMatch = true;
            }
        }
        $this->assertTrue($foundRepeatMatch);
    }

    public function testReturnsReverseDictionaryMatches()
    {
        $this->omniMatch->setPassword('drowssap');
        $matches = $this->omniMatch->getMatches();
        $foundReverseDictionaryMatch = false;
        foreach ($matches as $match) {
            if ($match['pattern'] === 'dictionary' and !empty($match['reversed'])) {
                $foundReverseDictionaryMatch = true;
            }
        }
        $this->assertTrue($foundReverseDictionaryMatch);
    }

    public function testReturnsSequenceMatches()
    {
        $this->omniMatch->setPassword('098765');
        $matches = $this->omniMatch->getMatches();
        $foundSequenceMatch = false;
        foreach ($matches as $match) {
            if ($match['pattern'] === 'sequence') {
                $foundSequenceMatch = true;
            }
        }
        $this->assertTrue($foundSequenceMatch);
    }

    public function testReturnsSpatialMatches()
    {
        $this->omniMatch->setPassword('cvbgfdert');
        $matches = $this->omniMatch->getMatches();
        $foundSpatialMatch = false;
        foreach ($matches as $match) {
            if ($match['pattern'] === 'spatial') {
                $foundSpatialMatch = true;
            }
        }
        $this->assertTrue($foundSpatialMatch);
    }
}
