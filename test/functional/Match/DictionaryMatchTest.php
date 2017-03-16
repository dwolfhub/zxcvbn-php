<?php
namespace test\functional\Match;

use Exception;
use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\DataProvider\FrequencyLists;
use Zxcvbn\Match\DictionaryMatch;

class DictionaryMatchTest extends TestCase
{
    private $testDicts = [
        'd1' => [
            'motherboard',
            'mother',
            'board',
            'abcd',
            'cdef',
        ],
        'd2' => [
            'z',
            '8',
            '99',
            '$',
            'asdf1234&*',
        ],
    ];

    public function testDictionaryMatches()
    {
        $matches = $this->dm('motherboard');
        $patterns = ['mother', 'motherboard', 'board'];
        $msg = 'matches words that contain other words';
        $this->checkMatches($msg, $matches, 'dictionary', $patterns, [[0, 5], [0, 10], [6, 10]], [
            'matched_word' => ['mother', 'motherboard', 'board'],
            'rank' => [2, 1, 3],
            'dictionary_name' => ['d1', 'd1', 'd1'],
        ]);

        $matches = $this->dm('abcdef');
        $patterns = ['abcd', 'cdef'];
        $msg = 'matches multiple words when they overlap';
        $this->checkMatches($msg, $matches, 'dictionary', $patterns, [[0, 3], [2, 5]], [
            'matched_word' => ['abcd', 'cdef'],
            'rank' => [4, 5],
            'dictionary_name' => ['d1', 'd1'],
        ]);

        $matches = $this->dm('BoaRdZ');
        $patterns = ['BoaRd', 'Z'];
        $msg = 'ignores uppercasing';
        $this->checkMatches($msg, $matches, 'dictionary', $patterns, [[0, 4], [5, 5]], [
            'matched_word' => ['board', 'z'],
            'rank' => [3, 1],
            'dictionary_name' => ['d1', 'd2'],
        ]);

        $prefixes = ['q', '%%'];
        $suffixes = ['%', 'qq'];
        $word = 'asdf1234&*';
        $msg = 'identifies words surrounded by non-words';
        foreach ($this->genPws($word, $prefixes, $suffixes) as $genPw) {
            $matches = $this->dm($genPw[0]);
            $this->checkMatches($msg, $matches, 'dictionary', [$word], [[$genPw[1], $genPw[2]]], [
                'matched_word' => [$word],
                'rank' => [5],
                'dictionary_name' => ['d2'],
            ]);
        }

        foreach ($this->testDicts as $name => $dict) {
            foreach ($dict as $rank => $word) {
                if ($word === 'motherboard') {
                    continue;
                }
                $rank += 1;

                $matches = $this->dm($word);
                $msg = sprintf('matches against all words in provided dictionaries: %s', $word);
                $this->checkMatches($msg, $matches, 'dictionary', [$word], [
                    [0, strlen($word) - 1],
                ], [
                    'matched_word' => [$word],
                    'rank' => [$rank],
                    'dictionary_name' => [$name],
                ]);
            }
        }

    }

    public function testDefaultDictionaries()
    {
        $dictionaryMatch = new DictionaryMatch();
        $dictionaryMatch->setRankedDictionaries(FrequencyLists::getData());
        $dictionaryMatch->setPassword('wow');
        $matches = $dictionaryMatch->getMatches();
        $patterns = ['wow'];
        $ijs = [[0, 2]];
        $msg = 'default dictionaries';
        $this->checkMatches($msg, $matches, 'dictionary', $patterns, $ijs, [
            'matched_word' => $patterns,
            'rank' => [322],
            'dictionary_name' => ['us_tv_and_film'],
        ]);
    }

    protected function checkMatches($prefix, $matches, $patternNames, $patterns, $ijs, $props)
    {
        $patternsCount = count($patterns);
        if (is_string($patternNames)) {
            $patternNames = array_fill(0, $patternsCount, $patternNames);
        }

        $isEqualLenArgs = count($patternNames) === $patternsCount and $patternsCount === count($ijs);
        foreach ($props as $prop => $lst) {
            if (!$isEqualLenArgs or count($lst) !== $patternsCount) {
                throw new Exception('unequal argument list to check_matches');
            }
        }

        $msg = sprintf('%s: count(matches) == %s', $prefix, $patternsCount);
        $this->assertEquals($patternsCount, count($matches), $msg);

        for ($k = 0; $k < $patternsCount; $k++) {
            $match = $matches[$k];
            $patternName = $patternNames[$k];
            $pattern = $patterns[$k];
            $i = $ijs[$k][0];
            $j = $ijs[$k][1];
            $msg = sprintf('%s: $matches[%s]["pattern"] == "%s"', $prefix, $k, $patternName);
            $this->assertEquals($patternName, $match['pattern'], $msg);

            $msg = sprintf('%s: $matches[%s] should have [i, j] of [%s, %s]', $prefix, $k, $i, $j);
            $this->assertEquals([$match['i'], $match['j']], [$i, $j], $msg);

            $msg = sprintf('%s: matches[%s]["token"] == "%s"', $prefix, $k, $pattern);
            $this->assertEquals($pattern, $match['token'], $msg);

            foreach ($props as $propName => $propList) {
                $propMsg = $propList[$k];
                if (is_string($propMsg)) {
                    $propMsg = sprintf("'%s'", $propMsg);
                }
                $msg = sprintf('%s: matches[%s].%s == %s', $prefix, $k, $propName, $propMsg);
                $this->assertEquals($propList[$k], $match[$propName], $msg);
            }
        }
    }

    /**
     * @param $pw
     * @return array
     */
    protected function dm($pw)
    {
        $dictionaryMatch = new DictionaryMatch();
        $dictionaryMatch->setRankedDictionaries($this->testDicts);
        $dictionaryMatch->setPassword($pw);

        return $dictionaryMatch->getMatches();
    }

    protected function genPws($pattern, $prefixes, $suffixes)
    {
        foreach ([$prefixes, $suffixes] as $lst) {
            if (in_array('', $lst)) {
                array_unshift($lst, '');
            }
        }
        $result = [];
        foreach ($prefixes as $prefix) {
            foreach ($suffixes as $suffix) {
                $i = strlen($prefix);
                $j = strlen($prefix) + strlen($pattern) - 1;
                array_push($result, [$prefix . $pattern . $suffix, $i, $j]);
            }
        }
        return $result;
    }
}