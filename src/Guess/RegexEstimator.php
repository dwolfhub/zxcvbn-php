<?php
namespace Zxcvbn\Guess;

use Zxcvbn\Scoring;

/**
 * Class RegexEstimator
 * @package Zxcvbn\Guess
 */
class RegexEstimator extends AbstractEstimator
{
    /**
     * {@inheritdoc}
     */
    public function estimate($match)
    {
        $charClassBases = [
            'alpha_lower' => 26,
            'alpha_upper' => 26,
            'alpha' => 52,
            'alphanumeric' => 62,
            'digits' => 10,
            'symbols' => 33,
        ];

        if (array_key_exists($match['regex_name'], $charClassBases)) {
            return $charClassBases[$match['regex_name']] ** strlen($match['token']);
        } else if ($match['regex_name'] === 'recent_year') {
            // conservative estimate of year space: num years from REFERENCE_YEAR.
            // if year is close to REFERENCE_YEAR, estimate a year space of
            // MIN_YEAR_SPACE.
            $yearSpace = abs((int)$match['regex_match'][0] - Scoring::REFERENCE_YEAR);
            $yearSpace = max($yearSpace, Scoring::MIN_YEAR_SPACE);

            return $yearSpace;
        }
    }
}