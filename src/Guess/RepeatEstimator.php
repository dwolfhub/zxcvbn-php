<?php
namespace Zxcvbn\Guess;

/**
 * Class RepeatEstimator
 * @package Zxcvbn\Guess
 */
class RepeatEstimator extends AbstractEstimator
{
    /**
     * {@inheritdoc}
     */
    public function estimate($match)
    {
        return $match['base_guesses'] * $match['repeat_count'];
    }
}