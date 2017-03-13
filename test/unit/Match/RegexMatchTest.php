<?php

namespace unit\test\Match;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\RegexMatch;

class RegexMatchTest extends TestCase
{
    /**
     * @var RegexMatch
     */
    protected $regexMatch;

    public function setUp()
    {
        $this->regexMatch = new RegexMatch();
    }

    public function testRecentYearRegex()
    {
        $this->regexMatch->setPassword('2015');
        $this->assertEquals([
            [
                'pattern' => 'regex',
                'token' => '2015',
                'i' => 0,
                'j' => 3,
                'regex_name' => 'recent_year',
                'regex_match' => ['2015'],
            ]
        ], $this->regexMatch->getMatches());

        $this->regexMatch->setPassword('1999');
        $this->assertEquals([
            [
                'pattern' => 'regex',
                'token' => '1999',
                'i' => 0,
                'j' => 3,
                'regex_name' => 'recent_year',
                'regex_match' => ['1999'],
            ]
        ], $this->regexMatch->getMatches());

        $this->regexMatch->setPassword('Test1999');
        $this->assertEquals([
            [
                'pattern' => 'regex',
                'token' => '1999',
                'i' => 4,
                'j' => 7,
                'regex_name' => 'recent_year',
                'regex_match' => ['1999'],
            ]
        ], $this->regexMatch->getMatches());

        $this->regexMatch->setPassword('1999Test');
        $this->assertEquals([
            [
                'pattern' => 'regex',
                'token' => '1999',
                'i' => 0,
                'j' => 3,
                'regex_name' => 'recent_year',
                'regex_match' => ['1999'],
            ]
        ], $this->regexMatch->getMatches());
    }
}
