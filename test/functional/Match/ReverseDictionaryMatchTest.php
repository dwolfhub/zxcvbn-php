<?php
namespace test\functional\Match;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\DictionaryMatch;
use Zxcvbn\Match\ReverseDictionaryMatch;

class ReverseDictionaryMatchTest extends AbstractFunctionalMatchTestCase
{
    protected $testDicts = [
        'd1' => [
            '123',
            '321',
            '456',
            '654',
        ],
    ];

    public function testReverseDictionaryMatches()
    {
        $password = '0123456789';

        $dictionaryMatch = new DictionaryMatch();
        $dictionaryMatch->setPassword(strrev($password));
        $dictionaryMatch->setRankedDictionaries($this->testDicts);

        $reverseDictionaryMatch = new ReverseDictionaryMatch();
        $reverseDictionaryMatch->setDictionaryMatch($dictionaryMatch);
        $reverseDictionaryMatch->setPassword($password);
        $matches = $reverseDictionaryMatch->getMatches();

        $this->checkMatches(
            'matches against reversed words',
            $matches,
            'dictionary',
            ['123', '456'],
            [[1, 3], [4, 6]],
            [
                'matched_word' => ['321', '654'],
                'reversed' => [true, true],
                'dictionary_name' => ['d1', 'd1'],
                'rank' => [2, 4],
            ]
        );
    }
}
