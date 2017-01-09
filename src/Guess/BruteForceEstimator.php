<?php
namespace Zxcvbn\Guess;

use Zxcvbn\Scoring;

/**
 * Class BruteForceEstimator
 * @package Zxcvbn\Guess
 */
class BruteForceEstimator extends AbstractEstimator
{
    /**
     * @var int
     */
    const BRUTEFORCE_CARDINALITY = 10;

    /**
     * {@inheritdoc}
     */
    public function estimate($match)
    {
        $tokenLen = strlen($match['token']);
        $guesses = self::BRUTEFORCE_CARDINALITY ** $tokenLen;
        // small detail: make bruteforce matches at minimum one guess bigger than
        // smallest allowed submatch guesses, such that non-bruteforce submatches
        // over the same [i..j] take precedence.
        if ($tokenLen === 1) {
            $minGuesses = Scoring::MIN_SUBMATCH_GUESSES_SINGLE_CHAR;
        } else {
            $minGuesses = Scoring::MIN_SUBMATCH_GUESSES_MULTI_CHAR;
        }

        return max($guesses, $minGuesses);
    }
}