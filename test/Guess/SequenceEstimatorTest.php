<?php
namespace ZxcvbnPhp\test\unit\Guess;

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

    public function testFirstCharObvious()
    {
        $this->assertEquals(24, $this->sequenceEstimator->estimate([
            'token' => 'abcdef',
            'ascending' => true
        ]));
    }

    public function testDigits()
    {
        $this->assertEquals(60, $this->sequenceEstimator->estimate([
            'token' => '234567',
            'ascending' => true
        ]));
    }

    public function testAlpha()
    {
        $this->assertEquals(78, $this->sequenceEstimator->estimate([
            'token' => 'bcd',
            'ascending' => true
        ]));
    }

    public function testNotAscending()
    {
        $this->assertEquals(156, $this->sequenceEstimator->estimate([
            'token' => 'bcd',
            'ascending' => false
        ]));
    }

    /**
     * Integration Testing
     * Some of these tests have been ported from the js and python
     * libraries to ensure consistency
     */

    /**
     * @param $token
     * @param $ascending
     * @param $guesses
     * @dataProvider sequenceGuessesDataProvider
     */
    public function testSequencePatternHasGuesses($token, $ascending, $guesses)
    {
        $match = [
            'token' => $token,
            'ascending' => $ascending,
        ];
        $this->assertEquals($guesses, $this->sequenceEstimator->estimate($match));
    }

    /**
     * @return array
     */
    public function sequenceGuessesDataProvider()
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
