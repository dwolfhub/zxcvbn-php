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

    /**
     * @var ReverseDictionaryMatch
     */
    protected $reverseDictionaryMatch;

    public function setUp()
    {
        $dictionaryMatch = new DictionaryMatch();
        $dictionaryMatch->setRankedDictionaries($this->testDicts);
        $this->reverseDictionaryMatch = new ReverseDictionaryMatch();
        $this->reverseDictionaryMatch->setDictionaryMatch($dictionaryMatch);
    }

    public function testReverseDictionaryMatches()
    {
        $this->reverseDictionaryMatch->setPassword('0123456789');
        $matches = $this->reverseDictionaryMatch->getMatches();
        $msg = 'matches against reversed words';

        $this->checkMatches($msg, $matches, 'dictionary', ['123', '456'], [[1, 3], [4, 6]], [
            'matched_word' => ['321', '654'],
            'reversed' => [true, true],
            'dictionary_name' => ['d1', 'd1'],
            'rank' => [2, 4],
        ]);
    }
}
