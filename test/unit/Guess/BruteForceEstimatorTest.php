<?php

namespace unit\Guess;

use PHPUnit_Framework_TestCase;
use Zxcvbn\Guess\BruteForceEstimator;
use Zxcvbn\Scoring;

/**
 * Class BruteForceEstimatorTest
 * @package unit\Guess
 */
class BruteForceEstimatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var BruteForceEstimator
     */
    protected $estimator;

    public function setUp()
    {
        $this->estimator = new BruteForceEstimator();
    }

    public function testSingleCharMinSubmatchGuesses()
    {
        $this->assertEquals(Scoring::MIN_SUBMATCH_GUESSES_SINGLE_CHAR, $this->estimator->estimate([
            'token' => 'a',
        ]));
    }

    public function testGuessesFromBruteforceCardinality()
    {
        $this->assertEquals(1e5, $this->estimator->estimate([
            'token' => 'abcde'
        ]));
    }
}