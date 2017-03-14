<?php
namespace ZxcvbnPhp\test\functional\Guess;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Guess\AbstractEstimator;
use Zxcvbn\Guess\RegexEstimator;
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

    public function testRegexGuesses()
    {
        $match = [
            'token' => 'aizocdk',
            'regex_name' => 'alpha_lower',
            'regex_match' => ['aizocdk'],
        ];
        $msg = "guesses of 26^7 for 7-char lowercase regex";
        $this->assertEquals(26 ** 7, $this->regexEstimator->estimate($match), $msg);

        $match = [
            'token' => 'ag7C8',
            'regex_name' => 'alphanumeric',
            'regex_match' => ['ag7C8'],
        ];
        $msg = "guesses of 62^5 for 5-char alphanumeric regex";
        $this->assertEquals((2 * 26 + 10) ** 5, $this->regexEstimator->estimate($match), $msg);


        $match = [
            'token' => '1972',
            'regex_name' => 'recent_year',
            'regex_match' => ['1972'],
        ];
        $msg = "guesses of |year - REFERENCE_YEAR| for distant year matches";
        $this->assertEquals(abs(Scoring::REFERENCE_YEAR - 1972), $this->regexEstimator->estimate($match), $msg);

        $match = [
            'token' => '2005',
            'regex_name' => 'recent_year',
            'regex_match' => ['2005'],
        ];
        $msg = "guesses of MIN_YEAR_SPACE for a year close to REFERENCE_YEAR";
        $this->assertEquals(AbstractEstimator::MIN_YEAR_SPACE, $this->regexEstimator->estimate($match), $msg);
    }
}