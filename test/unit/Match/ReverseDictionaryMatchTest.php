<?php

namespace unit\test\Match;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\DictionaryMatch;
use Zxcvbn\Match\ReverseDictionaryMatch;

class ReverseDictionaryMatchTest extends TestCase
{
    /**
     * @var ReverseDictionaryMatch
     */
    protected $reverseDictionaryMatch;

    public function setUp()
    {
        $mockDictMatch = $this->getMockBuilder(DictionaryMatch::class)
            ->setMethods(['getMatches'])
            ->getMock();
        $mockDictMatch->expects($this->once())
            ->method('getMatches')
            ->willReturn([
                [
                    'token' => 'foobar',
                    'i' => 0,
                    'j' => 5,
                ],
                [
                    'token' => 'foo',
                    'i' => 0,
                    'j' => 2,
                ],
                [
                    'token' => 'bar',
                    'i' => 3,
                    'j' => 5,
                ],
            ]);

        $this->reverseDictionaryMatch = new ReverseDictionaryMatch();
        $this->reverseDictionaryMatch->setRankedDictionaries([]);
        $this->reverseDictionaryMatch->setDictionaryMatch($mockDictMatch);
    }

    public function testReturnsResultsSorted()
    {
        $this->assertEquals([
            [
                'token' => 'rab',
                'i' => -6,
                'j' => -4,
                'reversed' => true,
            ],
            [
                'token' => 'raboof',
                'i' => -6,
                'j' => -1,
                'reversed' => true,
            ],
            [
                'token' => 'oof',
                'i' => -3,
                'j' => -1,
                'reversed' => true,
            ],
        ], $this->reverseDictionaryMatch->getMatches());
    }

}
