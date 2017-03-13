<?php
namespace ZxcvbnPhp\test\unit\Guess;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Guess\AbstractEstimator;
use Zxcvbn\Guess\RegexEstimator;
use Zxcvbn\Match\RegexMatch;
use Zxcvbn\Scoring;

class RegexEstimatorTest extends TestCase
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


    /**
     * Integration Testing
     * Some of these tests have been ported from the js and python
     * libraries to ensure consistency
     */

    public function testGuessesOf26ToThe7thFor7CharLowercaseRegex()
    {
        $match = [
            'token' => 'aizocdk',
            'regex_name' => 'alpha_lower',
            'regex_match' => ['aizocdk'],
        ];
        $this->assertEquals(26 ** 7, $this->regexEstimator->estimate($match));
    }

    public function testGuessesOf62ToThe5thFor5CharAlphanumericRegex()
    {
        $match = [
            'token' => 'ag7CB',
            'regex_name' => 'alphanumeric',
            'regex_match' => ['ag7CB'],
        ];
        $this->assertEquals((2 * 26 + 10) ** 5, $this->regexEstimator->estimate($match));
    }

    public function testGuessesOfAbsYearMinusReferenceYearForDistantYearMatches()
    {
        preg_match(RegexMatch::REGEXEN['recent_year'], '1972', $matches);
        $match = [
            'token' => '1972',
            'regex_name' => 'recent_year',
            'regex_match' => $matches
        ];
        $this->assertEquals(abs(Scoring::REFERENCE_YEAR - 1972), $this->regexEstimator->estimate($match));
    }

    public function testGuessesOfMinYearSpaceForAYearCloseToReferenceYear()
    {
        preg_match(RegexMatch::REGEXEN['recent_year'], '2005', $matches);
        $match = [
            'token' => '2005',
            'regex_name' => 'recent_year',
            'regex_match' => $matches
        ];
        $this->assertEquals(AbstractEstimator::MIN_YEAR_SPACE, $this->regexEstimator->estimate($match));
    }
}
