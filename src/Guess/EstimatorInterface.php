<?php
namespace Zxcvbn\Guess;

/**
 * Interface EstimatorInterface
 * @package Guess
 */
interface EstimatorInterface
{
    /**
     * @return int
     */
    public function estimate($match);
}