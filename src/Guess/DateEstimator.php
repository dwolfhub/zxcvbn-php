<?php
namespace Zxcvbn\Guess;

use Zxcvbn\Scoring;

/**
 * Class DateEstimator
 * @package Zxcvbn\Guess
 */
class DateEstimator extends AbstractEstimator
{
    /**
     * {@inheritdoc}
     */
    public function estimate($match)
    {
        $yearSpace = max(abs($match['year'] - Scoring::REFERENCE_YEAR), Scoring::MIN_YEAR_SPACE);
        $guesses = $yearSpace * 365;
        if (!empty($match['separator'])) {
            $guesses *= 4;
        }

        return $guesses;
    }
}