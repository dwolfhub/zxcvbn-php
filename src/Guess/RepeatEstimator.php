<?php
namespace ZxcvbnPhp\Guess;

/**
 * Class RepeatEstimator
 * @package ZxcvbnPhp\Guess
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