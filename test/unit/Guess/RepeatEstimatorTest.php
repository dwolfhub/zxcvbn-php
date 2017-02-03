<?php
namespace ZxcvbnPhp\test\unit\Guess;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Guess\RepeatEstimator;

class RepeatEstimatorTest extends TestCase
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
