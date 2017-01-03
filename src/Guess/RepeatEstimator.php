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
    public function estimate()
    {
        return $this->match['base_guesses'] * $this->match['repeat_count'];
    }
}