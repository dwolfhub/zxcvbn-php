<?php
namespace ZxcvbnPhp\test\unit\Guess;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Guess\EstimatorFactory;
use Zxcvbn\Guess\RepeatEstimator;
use Zxcvbn\Match\OmniMatch;
use Zxcvbn\Scoring;

class RepeatEstimatorTest extends TestCase
{
    /**
     * @var RepeatEstimator
     */
    protected $repeatEstimator;

    public function setUp()
    {
        $this->repeatEstimator = new RepeatEstimator();
    }

    public function testMultipliesBaseGuessesAndRepeatCount()
    {
        $this->assertEquals(24, $this->repeatEstimator->estimate([
            'base_guesses' => 12,
            'repeat_count' => 2,
        ]));
    }

}
