<?php
namespace test\functional\Guess;

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
            ['batterystaplebatterystaplebatterystaple', 'batterystaple', 3],
        ];
    }

}