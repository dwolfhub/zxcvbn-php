<?php

namespace unit\Match;

use Zxcvbn\Match\DateMatch;

class DateMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateMatch
     */
    protected $dateMatch;

    public function setUp()
    {
        $this->dateMatch = new DateMatch();
    }

    public function testReturnsMatchWithSeparators()
    {
        $this->dateMatch->setPassword('1.1.91');
        $this->assertEquals([
            [
                'pattern' => 'date',
                'token' => '1.1.91',
                'i' => 0,
                'j' => 5,
                'separator' => '.',
                'year' => 1991,
                'month' => 1,
                'day' => 1,
            ],
        ], $this->dateMatch->getMatches());

        $this->dateMatch->setPassword('johnsmith1.1.91');
        $this->assertEquals([
            [
                'pattern' => 'date',
                'token' => '1.1.91',
                'i' => 9,
                'j' => 14,
                'separator' => '.',
                'year' => 1991,
                'month' => 1,
                'day' => 1,
            ],
        ], $this->dateMatch->getMatches());

        $this->dateMatch->setPassword('1.1.16');
        $this->assertEquals([
            [
                'pattern' => 'date',
                'token' => '1.1.16',
                'i' => 0,
                'j' => 5,
                'separator' => '.',
                'year' => 2016,
                'month' => 1,
                'day' => 1,
            ],
        ], $this->dateMatch->getMatches());
    }

    public function testReturnsMatchWithoutSeparator()
    {
        $this->dateMatch->setPassword('1191');
        $this->assertEquals([
            [
                'pattern' => 'date',
                'token' => '1191',
                'i' => 0,
                'j' => 3,
                'separator' => '',
                'year' => 2001,
                'month' => 9,
                'day' => 11,
            ],
        ], $this->dateMatch->getMatches());

        $this->dateMatch->setPassword('johnsmith1191');
        $this->assertEquals([
            [
                'pattern' => 'date',
                'token' => '1191',
                'i' => 9,
                'j' => 12,
                'separator' => '',
                'year' => 2001,
                'month' => 9,
                'day' => 11,
            ],
        ], $this->dateMatch->getMatches());

        $this->dateMatch->setPassword('121213');
        $this->assertEquals([
            [
                'pattern' => 'date',
                'token' => '121213',
                'i' => 0,
                'j' => 5,
                'separator' => '',
                'year' => 2013,
                'month' => 12,
                'day' => 12,
            ],
        ], $this->dateMatch->getMatches());
    }

    public function testReturnsNoMatch()
    {
        $this->dateMatch->setPassword('testpassword');
        $this->assertEquals([], $this->dateMatch->getMatches());
    }
}
