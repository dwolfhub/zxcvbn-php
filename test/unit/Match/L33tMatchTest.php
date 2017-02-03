<?php

namespace unit\Match;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\DictionaryMatch;
use Zxcvbn\Match\L33tMatch;

class L33tMatchTest extends TestCase
{
    /**
     * @var L33tMatch
     */
    protected $l33tMatch;

    public function setUp()
    {
        $this->l33tMatch = new L33tMatch();

        $dictionaryMatch = new DictionaryMatch();
        $dictionaryMatch->setRankedDictionaries([
            'test_dictionary' => [
                'password',
            ]
        ]);
        $this->l33tMatch->setDictionaryMatch(
            $dictionaryMatch
        );

        $this->l33tMatch->setL33tTable(
            [
                'a' => ['4', '@'],
                'c' => ['(', '{', '[', '<'],
                'g' => ['6', '9'],
                'o' => ['0'],
            ]
        );
    }

    public function testL33tMatchOneSub()
    {
        $this->l33tMatch->setPassword('p@ssword');
        $this->assertEquals([
            [
                'pattern' => 'dictionary',
                'i' => 0,
                'j' => 7,
                'token' => 'p@ssword',
                'matched_word' => 'password',
                'rank' => 1,
                'dictionary_name' => 'test_dictionary',
                'reversed' => false,
                'l33t' => true,
                'sub' => [
                    '@' => 'a'
                ],
                'sub_display' => '@ -> a',
            ]
        ], $this->l33tMatch->getMatches());
    }

    public function testL33tMatchTwoSubs()
    {
        $this->l33tMatch->setPassword('p4ssw0rd');
        $this->assertEquals([
            [
                'pattern' => 'dictionary',
                'i' => 0,
                'j' => 7,
                'token' => 'p4ssw0rd',
                'matched_word' => 'password',
                'rank' => 1,
                'dictionary_name' => 'test_dictionary',
                'reversed' => false,
                'l33t' => true,
                'sub' => [
                    '4' => 'a',
                    '0' => 'o',
                ],
                'sub_display' => '4 -> a, 0 -> o',
            ]
        ], $this->l33tMatch->getMatches());
    }

    public function testNoL33tTable()
    {
        $this->l33tMatch->setL33tTable([]);
        $this->l33tMatch->setPassword('password');
        $this->assertEquals([], $this->l33tMatch->getMatches());
    }

    public function testDoesNotReturnMatchForDictionaryMatches()
    {
        $this->l33tMatch->setPassword('password');
        $this->assertEquals([], $this->l33tMatch->getMatches());
    }

}
