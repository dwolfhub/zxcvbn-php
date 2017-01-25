<?php

namespace unit\Match;

use Zxcvbn\Match\DictionaryMatch;
use Zxcvbn\Match\L33tMatch;

class L33tMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var L33tMatch
     */
    protected $l33tMatch;

    public function setUp()
    {
        $mockDictionaryMatch = $this->getMockBuilder(
            DictionaryMatch::class
        )
            ->setMethods(['getMatches'])
            ->getmock();
        $mockDictionaryMatch->expects($this->once())
            ->method('getMatches')
            ->willReturn([]);

        $this->l33tMatch = new L33tMatch();
        $this->l33tMatch->setDictionaryMatch(
            $mockDictionaryMatch
        );
        $this->l33tMatch->setL33tTable(
            L33tMatch::DEFAULT_L33T_TABLE
        );
    }

    public function testIsTesting()
    {
        $this->l33tMatch->setPassword('password');
        $this->assertEquals([], $this->l33tMatch->getMatches());
    }
}
