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

    public function testIsTesting()
    {
        $this->dateMatch->setPassword('password');
        $this->assertEquals([], $this->dateMatch->getMatches());
    }
}
