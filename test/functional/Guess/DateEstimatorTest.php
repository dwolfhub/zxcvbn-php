<?php
namespace test\functional\Guess;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Guess\AbstractEstimator;
use Zxcvbn\Guess\DateEstimator;
use Zxcvbn\Scoring;

class DateEstimatorTest extends TestCase
{
    public function testDateGuesses()
    {
        $dateEstimator = new DateEstimator();
        $match = [
            'token' => '1123',
            'separator' => '',
            'has_full_year' => false,
            'year' => 1923,
            'month' => 1,
            'day' => 1,
        ];
        $msg = sprintf('guesses for %s is 365 * distance_from_ref_year', $match['token']);
        $this->assertEquals(
            365 * abs(Scoring::REFERENCE_YEAR - $match['year']),
            $dateEstimator->estimate($match),
            $msg
        );

        $match = [
            'token' => '1/1/2010',
            'separator' => '/',
            'has_full_year' => true,
            'year' => 2010,
            'month' => 1,
            'day' => 1,
        ];
        $msg = 'recent years assume MIN_YEAR_SPACE. extra guesses are added for separators.';
        $this->assertEquals(365 * AbstractEstimator::MIN_YEAR_SPACE * 4, $dateEstimator->estimate($match), $msg);
    }
}