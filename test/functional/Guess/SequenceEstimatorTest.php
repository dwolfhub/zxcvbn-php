<?php
namespace test\functional\Guess;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Guess\SequenceEstimator;

class SequenceEstimatorTest extends TestCase
{
    /**
     * @var SequenceEstimator
     */
    protected $sequenceEstimator;

    public function setUp()
    {
        $this->sequenceEstimator = new SequenceEstimator();
    }

    /**
     * @dataProvider sequencePatternGuessesProvider
     */
    public function testSequencePatternGuesses($token, $ascending, $guesses)
    {
        $match = [
            'token' => $token,
            'ascending' =>$ascending,
        ];
        $this->assertEquals($guesses, $this->sequenceEstimator->estimate($match));
    }

    public function sequencePatternGuessesProvider()
    {
        return [
            ['ab', True, 4 * 2],  # obvious start * len-2
            ['XYZ', True, 26 * 3],  # base26 * len-3
            ['4567', True, 10 * 4],  # base10 * len-4
            ['7654', False, 10 * 4 * 2],  # base10 * len 4 * descending
            ['ZYX', False, 4 * 3 * 2],  # obvious start * len-3 * descending
        ];
    }
}