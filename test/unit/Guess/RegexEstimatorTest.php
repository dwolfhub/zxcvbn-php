<?php
namespace ZxcvbnPhp\test\unit\Guess;

use PHPUnit_Framework_TestCase;
use Zxcvbn\Guess\RegexEstimator;

class RegexEstimatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RegexEstimator
     */
    protected $regexEstimator;

    public function setUp()
    {
        $this->regexEstimator = new RegexEstimator();
    }

    public function testCharClassBasesExists()
    {
        $this->assertEquals(26, $this->regexEstimator->estimate([
            'regex_name' => 'alpha_lower',
            'token' => 'a',
        ]));
        $this->assertEquals(26, $this->regexEstimator->estimate([
            'regex_name' => 'alpha_upper',
            'token' => 'a',
        ]));
        $this->assertEquals(52, $this->regexEstimator->estimate([
            'regex_name' => 'alpha',
            'token' => 'a',
        ]));
        $this->assertEquals(62, $this->regexEstimator->estimate([
            'regex_name' => 'alphanumeric',
            'token' => 'a',
        ]));
        $this->assertEquals(10, $this->regexEstimator->estimate([
            'regex_name' => 'digits',
            'token' => 'a',
        ]));
        $this->assertEquals(33, $this->regexEstimator->estimate([
            'regex_name' => 'symbols',
            'token' => 'a',
        ]));
    }

    public function testRecentYearMinYearSpace()
    {
        preg_match('/\d{4}/', '2017', $match);
        $this->assertEquals(20, $this->regexEstimator->estimate([
            'regex_name' => 'recent_year',
            'regex_match' => $match,
        ]));
    }

    public function testRecentYearNotMinYearSpace()
    {
        preg_match('/\d{4}/', '1901', $match);
        $this->assertEquals(116, $this->regexEstimator->estimate([
            'regex_name' => 'recent_year',
            'regex_match' => $match,
        ]));
    }
}
