<?php

namespace test;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Guess\AbstractEstimator;
use Zxcvbn\Guess\EstimatorFactory;
use Zxcvbn\Scoring;

class ScoringTest extends TestCase
{
    /**
     * @var Scoring
     */
    protected $scoring;

    public function testMostGuessableMatchSequence()
    {
        $this->setUpUnitTest();
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
                ],
            ],
        ], $this->scoring->mostGuessableMatchSequence('password', []));
    }

    public function testMostGuessableMatchSequenceExcludeAdditive()
    {
        $this->setUpUnitTest();
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
                ],
            ],
        ], $this->scoring->mostGuessableMatchSequence('password', [], 1));
    }

    protected function setUpUnitTest()
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


    /**
     * @param int $i
     * @param int $j
     * @param int $guesses
     * @return array
     */
    protected function getMockMatchArray($i, $j, $guesses)
    {
        return [
            'i' => $i,
            'j' => $j,
            'guesses' => $guesses,
        ];
    }

}
