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

    public function testReturnsMatch()
    {
        $this->dateMatch->setPassword('1.1.91');
        $this->assertEquals([], $this->dateMatch->getMatches());
    }
}
