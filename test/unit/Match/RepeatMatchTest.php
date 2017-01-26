<?php

namespace unit\Match;

use Zxcvbn\Match\AbstractMatch;
use Zxcvbn\Match\RepeatMatch;
use Zxcvbn\Scoring;

class RepeatMatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RepeatMatch
     */
    protected $repeatMatch;

    public function setUp()
    {
        $scoring = $this->getMockBuilder(Scoring::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        $omniMatch = $this->getMockForAbstractClass(AbstractMatch::class);

        $this->repeatMatch = new RepeatMatch();
        $this->repeatMatch->setScoring($scoring);
        $this->repeatMatch->setOmniMatch($omniMatch);
    }

    public function testIsTesting()
    {
        $this->repeatMatch->setPassword('abcabc');
        $this->assertEquals([], $this->repeatMatch->getMatches());
    }
}
