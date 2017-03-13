<?php

namespace test\Guess;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Guess\AbstractEstimator;
use Zxcvbn\Guess\DateEstimator;
use Zxcvbn\Scoring;

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

    /**
     * Integration Testing
     * Some of these tests have been ported from the js and python
     * libraries to ensure consistency
     */

    public function testGuessesAre365TimesDistanceFromRefYear()
    {
        $match = [
            'token' => '1123',
            'separator' => '',
            'has_full_year' => false,
            'year' => 1923,
            'month' => 1,
            'day' => 1,
        ];
        $this->assertEquals(
            365 * abs(Scoring::REFERENCE_YEAR - $match['year']),
            $this->dateEstimator->estimate($match)
        );
    }

    public function testRecentYearsAssumeMinYearSpaceAndExtraGuessesAreAddedForSeparators()
    {
        $match = [
            'token' => '1/1/2010',
            'separator' => '/',
            'has_full_year' => true,
            'year' => 2010,
            'month' => 1,
            'day' => 1,
        ];
        $this->assertEquals(
            365 * AbstractEstimator::MIN_YEAR_SPACE * 4,
            $this->dateEstimator->estimate($match)
        );
    }
}
