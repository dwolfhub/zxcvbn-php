<?php

namespace unit\Match;

use Zxcvbn\Match\SequenceMatch;

class SequenceMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SequenceMatch
     */
    protected $sequenceMatch;

    public function setUp()
    {
        $this->sequenceMatch = new SequenceMatch();
    }

    public function testLower()
    {
        $this->sequenceMatch->setPassword('abcbabc');
        $this->assertEquals([
            [
                'pattern' => 'sequence',
                'i' => 0,
                'j' => 2,
                'token' => 'abc',
                'sequence_name' => 'lower',
                'sequence_space' => 26,
                'ascending' => true,
            ],
            [
                'pattern' => 'sequence',
                'i' => 2,
                'j' => 4,
                'token' => 'cba',
                'sequence_name' => 'lower',
                'sequence_space' => 26,
                'ascending' => false,
            ],
            [
                'pattern' => 'sequence',
                'i' => 4,
                'j' => 6,
                'token' => 'abc',
                'sequence_name' => 'lower',
                'sequence_space' => 26,
                'ascending' => true,
            ],
        ], $this->sequenceMatch->getMatches());
    }

    public function testUpper()
    {
        $this->sequenceMatch->setPassword('CBA');
        $this->assertEquals([
            [
                'pattern' => 'sequence',
                'i' => 0,
                'j' => 2,
                'token' => 'CBA',
                'sequence_name' => 'upper',
                'sequence_space' => 26,
                'ascending' => false,
            ]
        ], $this->sequenceMatch->getMatches());

        $this->sequenceMatch->setPassword('ABC');
        $this->assertEquals([
            [
                'pattern' => 'sequence',
                'i' => 0,
                'j' => 2,
                'token' => 'ABC',
                'sequence_name' => 'upper',
                'sequence_space' => 26,
                'ascending' => true,
            ]
        ], $this->sequenceMatch->getMatches());
    }

    public function testWithPrefixAndSuffix()
    {
        $this->sequenceMatch->setPassword('9j%CBA');
        $this->assertEquals([
            [
                'pattern' => 'sequence',
                'i' => 3,
                'j' => 5,
                'token' => 'CBA',
                'sequence_name' => 'upper',
                'sequence_space' => 26,
                'ascending' => false,
            ]
        ], $this->sequenceMatch->getMatches());

        $this->sequenceMatch->setPassword('ABC9j%');
        $this->assertEquals([
            [
                'pattern' => 'sequence',
                'i' => 0,
                'j' => 2,
                'token' => 'ABC',
                'sequence_name' => 'upper',
                'sequence_space' => 26,
                'ascending' => true,
            ]
        ], $this->sequenceMatch->getMatches());

        $this->sequenceMatch->setPassword('9j%ABC9j%');
        $this->assertEquals([
            [
                'pattern' => 'sequence',
                'i' => 3,
                'j' => 5,
                'token' => 'ABC',
                'sequence_name' => 'upper',
                'sequence_space' => 26,
                'ascending' => true,
            ]
        ], $this->sequenceMatch->getMatches());
    }
}
