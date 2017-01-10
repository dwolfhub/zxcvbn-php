<?php
namespace ZxcvbnPhp\test\unit\Guess;

use PHPUnit_Framework_TestCase;
use Zxcvbn\Guess\SequenceEstimator;

class SequenceEstimatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SequenceEstimator
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new SequenceEstimator();
    }

    public function testFirstCharObvious()
    {
        $this->assertEquals(24, $this->instance->estimate([
            'token' => 'abcdef',
            'ascending' => true
        ]));
    }

    public function testDigits()
    {
        $this->assertEquals(60, $this->instance->estimate([
            'token' => '234567',
            'ascending' => true
        ]));
    }

    public function testAlpha()
    {
        $this->assertEquals(78, $this->instance->estimate([
            'token' => 'bcd',
            'ascending' => true
        ]));
    }

    public function testNotAscending()
    {
        $this->assertEquals(156, $this->instance->estimate([
            'token' => 'bcd',
            'ascending' => false
        ]));
    }
}
