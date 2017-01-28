<?php

namespace unit\Match;

use Zxcvbn\Match\DateMatch;
use Zxcvbn\Match\MatchFactory;

class MatchFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MatchFactory
     */
    protected $matchFactory;

    public function setUp()
    {
        $this->matchFactory = new MatchFactory();
    }

    public function testReturnsDateMatch()
    {
        $this->assertInstanceOf(DateMatch::class, $this->matchFactory->create(MatchFactory::TYPE_DATE, 'testpassword'));
    }
}
