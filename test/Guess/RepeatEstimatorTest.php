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

    /**
     * Integration Testing
     * Some of these tests have been ported from the js and python
     * libraries to ensure consistency
     */

    /**
     * @param $token
     * @param $baseToken
     * @param $repeatCount
     * @dataProvider repeatGuessesDataProvider
     */
    public function testRepeatGuesses($token, $baseToken, $repeatCount)
    {
        $omniMatch = new OmniMatch();
        $omniMatch->setPassword($baseToken);

        $scoring = new Scoring(new EstimatorFactory());

        $baseGuesses = $scoring->mostGuessableMatchSequence(
            $baseToken,
            $omniMatch->getMatches()
        )['guesses'];

        $match = [
            'token' => $token,
            'base_token' => $baseToken,
            'base_guesses' => $baseGuesses,
            'repeat_count' => $repeatCount,
        ];
        $expectedGuesses = $baseGuesses * $repeatCount;
        $this->assertEquals($expectedGuesses, $this->repeatEstimator->estimate($match));
    }

    /**
     * @return array
     */
    public function repeatGuessesDataProvider()
    {
        return [
            ['aa', 'a', 2],
            ['999', '9', 3],
            ['$$$$', '$', 4],
            ['abab', 'ab', 2],
            ['batterystaplebatterystaplebatterystaple', 'batterystaple', 3]
        ];
    }
}
