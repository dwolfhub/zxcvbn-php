<?php
namespace ZxcvbnPhp\test\unit\Guess;

use PHPUnit_Framework_TestCase;
use Zxcvbn\Guess\RepeatEstimator;

class RepeatEstimatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RepeatEstimator
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new RepeatEstimator();
    }

    public function testMultipliesBaseGuessesAndRepeatCount()
    {
        $this->assertEquals(24, $this->instance->estimate([
            'base_guesses' => 12,
            'repeat_count' => 2,
        ]));
    }
}
