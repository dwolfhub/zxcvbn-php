<?php

namespace unit;

use Zxcvbn\Feedback;
use Zxcvbn\Match\AbstractMatch;
use Zxcvbn\Scoring;
use Zxcvbn\TimeEstimates;
use Zxcvbn\Zxcvbn;

class ZxcvbnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zxcvbn
     */
    protected $zxcvbn;

    public function setUp()
    {
        $mockMatch = $this->getMockBuilder(AbstractMatch::class)
            ->setMethods(['getMatches'])
            ->getMock();

        $mockScoring = $this->getMockBuilder(Scoring::class)
            ->disableOriginalConstructor()
            ->setMethods(['mostGuessableMatchSequence'])
            ->getMock();
        $mockScoring->expects($this->once())
            ->method('mostGuessableMatchSequence')
            ->willReturn([
                'guesses' => 1,
            ]);

        $mockTimeEstimates = $this->getMockBuilder(TimeEstimates::class)
            ->setMethods(['estimateAttackTimes'])
            ->getMock();
        $mockTimeEstimates->expects($this->once())
            ->method('estimateAttackTimes')
            ->willReturn();

        $mockFeedback = $this->getMockBuilder(Feedback::class)
            ->setMethods(['getFeedback'])
            ->getMock();

        $this->zxcvbn = new Zxcvbn($mockMatch, $mockScoring, $mockTimeEstimates, $mockFeedback);
    }

    public function testIsTesting()
    {
        // @todo remove
        $this->assertFalse(0, $this->zxcvbn->calculateStrength('password', []));
    }
}
