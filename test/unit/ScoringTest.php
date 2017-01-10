<?php

namespace unit;

use Zxcvbn\Guess\AbstractEstimator;
use Zxcvbn\Guess\EstimatorFactory;
use Zxcvbn\Scoring;

class ScoringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Scoring
     */
    protected $scoring;

    public function setUp()
    {
        $mockEstimator = $this->getMockBuilder(
            AbstractEstimator::class
        )
            ->setMethods(['estimate'])
            ->getMock();
        $mockEstimator->expects($this->atLeastOnce())
            ->method('estimate')
            ->willReturn(1e3);

        $mockEstimatorFactory = $this->getMockBuilder(
            EstimatorFactory::class
        )
            ->setMethods(['create'])
            ->getMock();
        $mockEstimatorFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($mockEstimator);

        $this->scoring = new Scoring($mockEstimatorFactory);
    }

    public function testMostGuessableMatchSequence()
    {
        $this->assertEquals([
            'password' => 'password',
            'guesses' => 1001.0,
            'guesses_log10' => 3.0004340774793188,
            'sequence' => [
                [
                    'pattern' => 'bruteforce',
                    'token' => 'password',
                    'i' => 0,
                    'j' => 7,
                    'guesses' => 1000.0,
                    'guesses_log10' => 3.0,
                ]
            ],
        ], $this->scoring->mostGuessableMatchSequence('password', []));
    }

    public function testMostGuessableMatchSequenceExcludeAdditive()
    {
        $this->assertEquals([
            'password' => 'password',
            'guesses' => 1000.0,
            'guesses_log10' => 3.0,
            'sequence' => [
                [
                    'pattern' => 'bruteforce',
                    'token' => 'password',
                    'i' => 0,
                    'j' => 7,
                    'guesses' => 1000.0,
                    'guesses_log10' => 3.0,
                ]
            ],
        ], $this->scoring->mostGuessableMatchSequence('password', [], 1));
    }
}
