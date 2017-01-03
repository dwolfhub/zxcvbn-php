<?php
namespace Zxcvbn\Guess;

/**
 * Class SequenceEstimator
 * @package Zxcvbn\Guess
 */
class SequenceEstimator extends AbstractEstimator
{

    /**
     * {@inheritdoc}
     */
    public function estimate()
    {
        $firstChr = substr($this->match['token'], 0, 1);
        // lower guesses for obvious starting points
        if (in_array($firstChr, ['a', 'A', 'z', 'Z', '0', '1', '9'])) {
            $baseGuesses = 4;
        } else {
            if (preg_match('/\d/', $firstChr)) {
                $baseGuesses = 10; // digits
            } else {
                // could give a higher base for uppercase,
                // assigning 26 to both upper and lower sequences is more
                // conservative.
                $baseGuesses = 26;
            }
        }

        if (!$this->match['ascending']) {
            $baseGuesses *= 2;
        }

        return $baseGuesses * strlen($this->match['token']);
    }
}