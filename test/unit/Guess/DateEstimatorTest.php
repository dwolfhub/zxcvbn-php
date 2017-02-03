<?php

namespace unit\Guess;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Guess\DateEstimator;

class DateEstimatorTest extends TestCase
{
    /**
     * @var DateEstimator
     */
    protected $dateEstimator;

    public function setUp()
    {
        $this->dateEstimator = new DateEstimator();
    }

    public function testUsesMinYearSpace()
    {
        $this->assertEquals(7300, $this->dateEstimator->estimate([
            'year' => 2017,
            'separator' => false,
        ]));
    }

    public function testGuessesWithNoSeparator()
    {
        $this->assertEquals(12045, $this->dateEstimator->estimate([
            'year' => 1984,
            'separator' => false,
        ]));
    }

    public function testGuessesWithSeparator()
    {
        $this->assertEquals(48180, $this->dateEstimator->estimate([
            'year' => 1984,
            'separator' => true,
        ]));
    }
}
