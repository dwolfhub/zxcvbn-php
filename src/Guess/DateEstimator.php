<?php
namespace ZxcvbnPhp\Guess;

use ZxcvbnPhp\Scoring;

/**
 * Class DateEstimator
 * @package ZxcvbnPhp\Guess
 */
class DateEstimator extends AbstractEstimator
{
    /**
     * {@inheritdoc}
     */
    public function estimate()
    {
        $yearSpace = max(abs($this->match['year'] - Scoring::REFERENCE_YEAR), Scoring::MIN_YEAR_SPACE);
        $guesses = $yearSpace * 365;
        if (!empty($match['separator'])) {
            $guesses *= 4;
        }

        return $guesses;
    }
}