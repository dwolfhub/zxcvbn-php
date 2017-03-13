<?php

namespace unit\test\Guess;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Guess\DictionaryEstimator;

class DictionaryEstimatorTest extends TestCase
{
    /**
     * @var DictionaryEstimator
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new DictionaryEstimator();
    }

    public function testUppercaseVariationsAllLower()
    {
        $this->assertEquals(1, $this->instance->estimate([
            'rank' => 1,
            'token' => 'abcdefg',
        ]));
    }

    public function testUppercaseVariationsStartUpper()
    {
        $this->assertEquals(2, $this->instance->estimate([
            'rank' => 1,
            'token' => 'Password',
        ]));
    }

    public function testUppercaseVariationsEndUpper()
    {
        $this->assertEquals(2, $this->instance->estimate([
            'rank' => 1,
            'token' => 'passworD',
        ]));
    }

    public function testUppercaseVariationsAllUpper()
    {
        $this->assertEquals(2, $this->instance->estimate([
            'rank' => 1,
            'token' => 'PASSWORD',
        ]));
    }

    public function testUppercaseVariationsNoRegexMatches()
    {
        $this->assertEquals(37, $this->instance->estimate([
            'rank' => 1,
            'token' => 'pAssWord',
        ]));
    }

    public function testL33tVariationsNoUnsubbed()
    {
        $this->assertEquals(2, $this->instance->estimate([
            'rank' => 1,
            'token' => 'p@ssword',
            'l33t' => true,
            'sub' => [
                '@' => 'a',
            ]
        ]));
    }

    public function testL33tVariationsNoSubbed()
    {
        $this->assertEquals(2, $this->instance->estimate([
            'rank' => 1,
            'token' => 'password',
            'l33t' => true,
            'sub' => [
                '@' => 'a',
            ]
        ]));
    }

    public function testL33tVariationsSubbedAndUnsubbed()
    {
        $this->assertEquals(3, $this->instance->estimate([
            'rank' => 1,
            'token' => '@ll@board',
            'l33t' => true,
            'sub' => [
                '@' => 'a',
            ]
        ]));
    }

    public function testReversedVariations()
    {
        $this->assertEquals(2, $this->instance->estimate([
            'rank' => 1,
            'token' => 'abcdef',
            'l33t' => false,
            'reversed' => true,
        ]));
    }
}
