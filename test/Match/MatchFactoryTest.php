<?php

namespace test\Match;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\DateMatch;
use Zxcvbn\Match\DictionaryMatch;
use Zxcvbn\Match\L33tMatch;
use Zxcvbn\Match\MatchFactory;
use Zxcvbn\Match\OmniMatch;
use Zxcvbn\Match\RegexMatch;
use Zxcvbn\Match\RepeatMatch;
use Zxcvbn\Match\ReverseDictionaryMatch;
use Zxcvbn\Match\SequenceMatch;
use Zxcvbn\Match\SpatialMatch;
use Zxcvbn\Scoring;

class MatchFactoryTest extends TestCase
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

    public function testReturnsDictionaryMatch()
    {
        $this->assertInstanceOf(
            DictionaryMatch::class,
            $this->matchFactory->create(MatchFactory::TYPE_DICTIONARY, 'testpassword')
        );
    }

    public function testReturnsL33tMatch()
    {
        /** @var L33tMatch $l33tMatch */
        $l33tMatch = $this->matchFactory->create(MatchFactory::TYPE_L33T, 'testpassword');
        $this->assertInstanceOf(L33tMatch::class, $l33tMatch);

        $this->assertInternalType('array', $l33tMatch->getL33tTable());
        $this->assertEquals(L33tMatch::DEFAULT_L33T_TABLE, $l33tMatch->getL33tTable());

        $this->assertInstanceOf(DictionaryMatch::class, $l33tMatch->getDictionaryMatch());
        $this->assertEquals('testpassword', $l33tMatch->getDictionaryMatch()->getPassword());

    }

    public function testReturnsOmniMatch()
    {
        $this->assertInstanceOf(
            OmniMatch::class,
            $this->matchFactory->create(MatchFactory::TYPE_OMNI, 'testpassword')
        );
    }

    public function testReturnsRegexMatch()
    {
        $this->assertInstanceOf(
            RegexMatch::class,
            $this->matchFactory->create(MatchFactory::TYPE_REGEX, 'testpassword')
        );
    }

    public function testReturnsRepeatMatch()
    {
        /** @var RepeatMatch $repeatMatch */
        $repeatMatch = $this->matchFactory->create(MatchFactory::TYPE_REPEAT, 'testpassword');
        $this->assertInstanceOf(RepeatMatch::class, $repeatMatch);

        $this->assertInstanceOf(Scoring::class, $repeatMatch->getScoring());
        $this->assertInstanceOf(OmniMatch::class, $repeatMatch->getOmniMatch());
        $this->assertEquals('testpassword', $repeatMatch->getOmniMatch()->getPassword());
    }

    public function testReturnsReverseDictionaryMatch()
    {
        /** @var ReverseDictionaryMatch $reverseDictMatch */
        $reverseDictMatch = $this->matchFactory->create(MatchFactory::TYPE_REVERSE_DICTIONARY, 'testpassword');
        $this->assertInstanceOf(ReverseDictionaryMatch::class, $reverseDictMatch);
        $this->assertInstanceOf(DictionaryMatch::class, $reverseDictMatch->getDictionaryMatch());
        $this->assertEquals('drowssaptset', $reverseDictMatch->getDictionaryMatch()->getPassword());
    }

    public function testReturnsSequenceMatch()
    {
        $this->assertInstanceOf(
            SequenceMatch::class,
            $this->matchFactory->create(MatchFactory::TYPE_SEQUENCE, 'testpassword')
        );
    }

    public function testReturnsSpatialMatch()
    {
        /** @var SpatialMatch $spatialMatch */
        $spatialMatch = $this->matchFactory->create(MatchFactory::TYPE_SPATIAL, 'testpassword');
        $this->assertInstanceOf(SpatialMatch::class, $spatialMatch);
        $this->assertInternalType('array', $spatialMatch->getGraphs());
        $this->assertNotEmpty($spatialMatch->getGraphs());
    }

    public function testThrowsExceptionWhenBadType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('foobar is not a valid Match type');

        $this->matchFactory->create('foobar', 'testpassword');
    }
}
