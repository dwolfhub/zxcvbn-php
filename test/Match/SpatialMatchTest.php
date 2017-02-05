<?php

namespace test\Match;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\DataProvider\AdjacencyGraphs;
use Zxcvbn\Match\SpatialMatch;

class SpatialMatchTest extends TestCase
{
    /**
     * @var SpatialMatch
     */
    protected $spatialMatch;

    public function setUp()
    {
        $this->spatialMatch = new SpatialMatch();
    }

    public function testNoGraphs()
    {
        $this->spatialMatch->setPassword('abcdef');
        $this->assertEquals([], $this->spatialMatch->getMatches());
    }

    public function testNoResults()
    {
        $this->spatialMatch->setGraphs(AdjacencyGraphs::getData());
        $this->spatialMatch->setPassword('testpassword');
        $this->assertEquals([], $this->spatialMatch->getMatches());
    }

    public function testAdjacencyGraphQwerty()
    {
        $this->spatialMatch->setGraphs(AdjacencyGraphs::getData());

        $this->spatialMatch->setPassword('qazxswedc');
        $this->assertEquals([
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 8,
                'token' => 'qazxswedc',
                'graph' => 'qwerty',
                'turns' => 5,
                'shifted_count' => 0,
            ]
        ], $this->spatialMatch->getMatches());

        $this->spatialMatch->setPassword('testqazxswedc');
        $this->assertEquals([
            [
                'pattern' => 'spatial',
                'i' => 4,
                'j' => 12,
                'token' => 'qazxswedc',
                'graph' => 'qwerty',
                'turns' => 5,
                'shifted_count' => 0,
            ]
        ], $this->spatialMatch->getMatches());

        $this->spatialMatch->setPassword('qazxswedctest');
        $this->assertEquals([
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 8,
                'token' => 'qazxswedc',
                'graph' => 'qwerty',
                'turns' => 5,
                'shifted_count' => 0,
            ]
        ], $this->spatialMatch->getMatches());

        $this->spatialMatch->setPassword('bnmkjh');
        $this->assertEquals([
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 5,
                'token' => 'bnmkjh',
                'graph' => 'qwerty',
                'turns' => 3,
                'shifted_count' => 0,
            ]
        ], $this->spatialMatch->getMatches());
    }

    public function testAdjacencyGraphDvorak()
    {
        $this->spatialMatch->setGraphs(AdjacencyGraphs::getData());

        $this->spatialMatch->setPassword('`1~!23');
        $this->assertEquals([
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 5,
                'token' => '`1~!23',
                'graph' => 'dvorak',
                'turns' => 3,
                'shifted_count' => 2,
            ],
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 5,
                'token' => '`1~!23',
                'graph' => 'qwerty',
                'turns' => 3,
                'shifted_count' => 2,
            ]
        ], $this->spatialMatch->getMatches());

        $this->spatialMatch->setPassword('0(*&^54');
        $this->assertEquals([
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 6,
                'token' => '0(*&^54',
                'graph' => 'dvorak',
                'turns' => 1,
                'shifted_count' => 4,
            ],
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 6,
                'token' => '0(*&^54',
                'graph' => 'qwerty',
                'turns' => 1,
                'shifted_count' => 4,
            ]
        ], $this->spatialMatch->getMatches());

        $this->spatialMatch->setPassword('test0(*&^54');
        $this->assertEquals([
            [
                'pattern' => 'spatial',
                'i' => 4,
                'j' => 10,
                'token' => '0(*&^54',
                'graph' => 'dvorak',
                'turns' => 1,
                'shifted_count' => 4,
            ],
            [
                'pattern' => 'spatial',
                'i' => 4,
                'j' => 10,
                'token' => '0(*&^54',
                'graph' => 'qwerty',
                'turns' => 1,
                'shifted_count' => 4,
            ]
        ], $this->spatialMatch->getMatches());

        $this->spatialMatch->setPassword('0(*&^54test');
        $this->assertEquals([
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 6,
                'token' => '0(*&^54',
                'graph' => 'dvorak',
                'turns' => 1,
                'shifted_count' => 4,
            ],
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 6,
                'token' => '0(*&^54',
                'graph' => 'qwerty',
                'turns' => 1,
                'shifted_count' => 4,
            ]
        ], $this->spatialMatch->getMatches());
    }

    public function testAdjacencyGraphsKeypad()
    {
        $this->spatialMatch->setGraphs(AdjacencyGraphs::getData());

        $this->spatialMatch->setPassword('-+9');
        $this->assertEquals([
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 2,
                'token' => '-+9',
                'graph' => 'mac_keypad',
                'turns' => 2,
                'shifted_count' => 0,
            ],
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 2,
                'token' => '-+9',
                'graph' => 'keypad',
                'turns' => 2,
                'shifted_count' => 0,
            ]
        ], $this->spatialMatch->getMatches());
    }

    public function testAdjacencyGraphsMacKeypad()
    {
        $this->spatialMatch->setGraphs(AdjacencyGraphs::getData());

        $this->spatialMatch->setPassword('147852369');
        $this->assertEquals([
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 8,
                'token' => '147852369',
                'graph' => 'mac_keypad',
                'turns' => 5,
                'shifted_count' => 0,
            ],
            [
                'pattern' => 'spatial',
                'i' => 0,
                'j' => 8,
                'token' => '147852369',
                'graph' => 'keypad',
                'turns' => 5,
                'shifted_count' => 0,
            ]
        ], $this->spatialMatch->getMatches());
    }
}
