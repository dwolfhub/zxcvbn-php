<?php

namespace test\Match;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\DictionaryMatch;

class DictionaryMatchTest extends TestCase
{
    /**
     * @var DictionaryMatch
     */
    protected $dictionaryMatch;

    public function setUp()
    {
        $this->dictionaryMatch = new DictionaryMatch();
    }

    public function testMatchesDictionary()
    {
        $this->dictionaryMatch->addRankedDictionary('test_ranked_dict', ['foo', 'bar',]);
        $this->dictionaryMatch->setPassword('foo');
        $this->assertEquals([
            [
                'pattern' => 'dictionary',
                'i' => 0,
                'j' => 2,
                'token' => 'foo',
                'matched_word' => 'foo',
                'rank' => 1,
                'dictionary_name' => 'test_ranked_dict',
                'reversed' => false,
                'l33t' => false,
            ],
        ], $this->dictionaryMatch->getMatches());

        $this->dictionaryMatch->setPassword('bar');
        $this->assertEquals([
            [
                'pattern' => 'dictionary',
                'i' => 0,
                'j' => 2,
                'token' => 'bar',
                'matched_word' => 'bar',
                'rank' => 2,
                'dictionary_name' => 'test_ranked_dict',
                'reversed' => false,
                'l33t' => false,
            ],
        ], $this->dictionaryMatch->getMatches());

        $this->dictionaryMatch->setPassword('johnsmithbar');
        $this->assertEquals([
            [
                'pattern' => 'dictionary',
                'i' => 9,
                'j' => 11,
                'token' => 'bar',
                'matched_word' => 'bar',
                'rank' => 2,
                'dictionary_name' => 'test_ranked_dict',
                'reversed' => false,
                'l33t' => false,
            ],
        ], $this->dictionaryMatch->getMatches());
    }
}
