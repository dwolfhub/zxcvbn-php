<?php

namespace test;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Feedback;
use Zxcvbn\Match\AbstractMatch;
use Zxcvbn\Scoring;
use Zxcvbn\TimeEstimates;
use Zxcvbn\Zxcvbn;

class ZxcvbnTest extends TestCase
{
    public function testCalculateStrength()
    {
        $mockMatch = $this->getMockBuilder(AbstractMatch::class)
            ->setMethods(['getMatches', 'addRankedDictionary'])
            ->getMock();
        $mockMatch->expects($this->once())
            ->method('getMatches')
            ->willReturn(['mockmatch']);
        $mockMatch->expects($this->once())
            ->method('addRankedDictionary')
            ->with('user_inputs', ['pass', 'word']); // @todo

        $mockScoring = $this->getMockBuilder(Scoring::class)
            ->disableOriginalConstructor()
            ->setMethods(['mostGuessableMatchSequence'])
            ->getMock();
        $mockScoring->expects($this->once())
            ->method('mostGuessableMatchSequence')
            ->with('password', ['mockmatch'])
            ->willReturn([
                'guesses' => 1,
                'sequence' => ['mocksequence'],
            ]);

        $mockTimeEstimates = $this->getMockBuilder(TimeEstimates::class)
            ->setMethods(['estimateAttackTimes'])
            ->getMock();
        $mockTimeEstimates->expects($this->once())
            ->method('estimateAttackTimes')
            ->willReturn([
                'score' => 1,
            ]);

        $mockFeedback = $this->getMockBuilder(Feedback::class)
            ->setMethods(['getFeedback'])
            ->getMock();
        $mockFeedback->expects($this->once())
            ->method('getFeedback')
            ->willReturn([
                'warning' => 'THIS IS A WARNING',
                'suggestions' => [
                    'SUGGESTION 1',
                    'SUGGESTION 2',
                ],
            ]);

        $zxcvbn = new Zxcvbn(
            $mockMatch,
            $mockScoring,
            $mockTimeEstimates,
            $mockFeedback
        );

        $strength = $zxcvbn->calculateStrength('password', ['pass', 'word']);

        $this->assertEquals(1, $strength['guesses']);
        $this->assertEquals(['mocksequence'], $strength['sequence']);
        $this->assertEquals(1, $strength['score']);
        $this->assertEquals('THIS IS A WARNING', $strength['feedback']['warning']);
        $this->assertContains('SUGGESTION 1', $strength['feedback']['suggestions']);
        $this->assertContains('SUGGESTION 2', $strength['feedback']['suggestions']);
        $this->assertInternalType('float', $strength['calc_time']);
        $this->assertLessThan(.1, $strength['calc_time']);
    }

}
