<?php

namespace unit\Match;

use Zxcvbn\Match\AbstractMatch;
use Zxcvbn\Match\RepeatMatch;
use Zxcvbn\Scoring;

class RepeatMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RepeatMatch
     */
    protected $repeatMatch;

    public function setUp()
    {
        $this->repeatMatch = new RepeatMatch();
    }

    public function testBasicRepeat()
    {
        $omniMatch = $this->getMockForAbstractClass(AbstractMatch::class);
        $omniMatch->expects($this->once())
            ->method('getMatches')
            ->willReturn([]);
        $this->repeatMatch->setOmniMatch($omniMatch);

        $scoring = $this->getMockBuilder(Scoring::class)
            ->disableOriginalConstructor()
            ->setMethods(['mostGuessableMatchSequence'])
            ->getMock();
        $scoring->expects($this->once())
            ->method('mostGuessableMatchSequence')
            ->willReturn([
                'sequence' => 'testsequence',
                'guesses' => 987654321
            ]);
        $this->repeatMatch->setScoring($scoring);

        $this->repeatMatch->setPassword('abcabc');
        $this->assertEquals([
            [
                'pattern' => 'repeat',
                'i' => 0,
                'j' => 5,
                'token' => 'abcabc',
                'base_token' => 'abc',
                'base_guesses' => 987654321,
                'base_matches' => 'testsequence',
                'repeat_count' => 2,
            ]
        ], $this->repeatMatch->getMatches());
    }

    public function testMultipleRepeated()
    {
        $omniMatch = $this->getMockForAbstractClass(AbstractMatch::class);
        $omniMatch->method('getMatches')
            ->willReturn([]);
        $this->repeatMatch->setOmniMatch($omniMatch);

        $scoring = $this->getMockBuilder(Scoring::class)
            ->disableOriginalConstructor()
            ->setMethods(['mostGuessableMatchSequence'])
            ->getMock();
        $scoring->method('mostGuessableMatchSequence')
            ->willReturn([
                'sequence' => 'testsequence',
                'guesses' => 987654321
            ]);
        $this->repeatMatch->setScoring($scoring);

        $this->repeatMatch->setPassword('aaaaaaaa');
        $this->assertEquals([
            [
                'pattern' => 'repeat',
                'i' => 0,
                'j' => 7,
                'token' => 'aaaaaaaa',
                'base_token' => 'a',
                'base_guesses' => 987654321,
                'base_matches' => 'testsequence',
                'repeat_count' => 8,
            ]
        ], $this->repeatMatch->getMatches());
    }
}
