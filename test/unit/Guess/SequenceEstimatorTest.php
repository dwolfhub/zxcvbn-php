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

}
