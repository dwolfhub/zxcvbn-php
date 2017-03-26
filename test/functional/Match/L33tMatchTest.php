<?php

namespace test\functional\Match;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Zxcvbn\Match\DictionaryMatch;
use Zxcvbn\Match\L33tMatch;

class L33tMatchTest extends AbstractFunctionalMatchTestCase
{
    private $testTable = [
        'a' => ['4', '@'],
        'c' => ['(', '{', '[', '<'],
        'g' => ['6', '9'],
        'o' => ['0'],
    ];

    private $testDicts = [
        'words' => [
            'aac',
            'password',
            'paassword',
            'asdf0',
        ],
        'words2' => [
            'cgo',
        ],
    ];

    public function testTranslate()
    {
        $chrMap = [
            'a' => 'A',
            'b' => 'B',
        ];

        $l33tMatch = new L33tMatch();
        $reflectionClass = new ReflectionClass(L33tMatch::class);
        $translateMethod = $reflectionClass->getMethod('translate');
        $translateMethod->setAccessible(true);

        foreach ([
                     ['a', $chrMap, 'A'],
                     ['c', $chrMap, 'c'],
                     ['ab', $chrMap, 'AB'],
                     ['abc', $chrMap, 'ABc'],
                     ['aa', $chrMap, 'AA'],
                     ['abab', $chrMap, 'ABAB'],
                     ['', $chrMap, ''],
                     ['', [], ''],
                     ['abc', [], 'abc'],
                 ] as list($string, $map, $result)) {
            $l33tMatch->setPassword($string);
            $msg = sprintf("translates '%s' to '%s' with provided charmap", $string, $result);
            $this->assertEquals($result, $translateMethod->invokeArgs($l33tMatch, [$map]), $msg);
        }
    }

    public function testRelevantL33tSubtable()
    {
        $l33tMatch = new L33tMatch();

        $reflection = new ReflectionClass(L33tMatch::class);
        $method = $reflection->getMethod('relevantL33tSubtable');
        $method->setAccessible(true);

        $msg = 'reduces l33t table to only the substitutions that a password might be employing';
        foreach ([
                     ['', []],
                     ['abcdefgo123578!#$&*)]}>', []],
                     ['a', []],
                     ['4', ['a' => ['4']]],
                     ['4@', ['a' => ['4', '@']]],
                     ['4({60', ['a' => ['4'], 'c' => ['(', '{'], 'g' => ['6'], 'o' => ['0']]],
                 ] as list($pw, $expected)) {
            $l33tMatch->setPassword($pw);

            $this->assertEquals($expected, $method->invokeArgs($l33tMatch, [$this->testTable]), $msg);
        }
    }

    public function testEnumerateL33tSubs()
    {
        $l33tMatch = new L33tMatch();

        $reflection = new ReflectionClass(L33tMatch::class);
        $method = $reflection->getMethod('enumerateL33tSubs');
        $method->setAccessible(true);

        $msg = 'enumerates the different sets of l33t substitutions a password might be using';

        foreach ([
                     [[], [[]]],
                     [['a' => ['@']], [['@' => 'a']]],
                     [['a' => ['@', '4']], [['@' => 'a'], ['4' => 'a']]],
                     [
                         ['a' => ['@', '4'], 'c' => ['(']],
                         [['@' => 'a', '(' => 'c'], ['4' => 'a', '(' => 'c']],
                     ],
                 ] as list($table, $subs)) {
            $l33tMatch->setL33tTable($table);
            $this->assertEquals($subs, $method->invokeArgs($l33tMatch, [$table]), $msg);
        }
    }

    public function testL33tMatch()
    {
        $this->assertEquals([], $this->lm(''), "doesn't match ''");
        $this->assertEquals([], $this->lm('password'), "doesn't match pure dictionary words");

        $msg = 'matches against common l33t substitutions';
        foreach ([
                     ['p4ssword', 'p4ssword', 'password', 'words', 2, [0, 7], ['4' => 'a']],
                     ['p@ssw0rd', 'p@ssw0rd', 'password', 'words', 2, [0, 7], ['@' => 'a', '0' => 'o'],],
                     ['aSdfO{G0asDfO', '{G0', 'cgo', 'words2', 1, [5, 7], ['{' => 'c', '0' => 'o'],],
                 ] as list($password, $pattern, $word, $dictionaryName, $rank, $ij, $sub)) {
            $this->checkMatches($msg, $this->lm($password), 'dictionary', [$pattern], [$ij], [
                'l33t' => [true],
                'sub' => [$sub],
                'matched_word' => [$word],
                'rank' => [$rank],
                'dictionary_name' => [$dictionaryName],
            ]);
        }

        $msg = 'matches against overlapping l33t patterns';
        $matches = $this->lm('@a(go{G0');
        $this->checkMatches($msg, $matches, 'dictionary', ['@a(', '(go', '{G0'],
            [[0, 2], [2, 4], [5, 7]], [
                'l33t' => [true, true, true],
                'sub' => [
                    ['@' => 'a', '(' => 'c'],
                    ['(' => 'c'],
                    ['{' => 'c', '0' => 'o'],
                ],
                'matched_word' => ['aac', 'cgo', 'cgo'],
                'rank' => [1, 1, 1],
                'dictionary_name' => ['words', 'words2', 'words2'],
            ]);

        $msg = 'doesn\'t match when multiple l33t substitutions are needed for the same letter';
        $this->assertEquals($this->lm('p4@ssword'), [], $msg);

        $msg = 'doesn\'t match single-character l33ted words';
        $this->assertEquals([], $this->lm('4 1 @'), $msg);

        # known issue: subsets of substitutions aren't tried.
        # for long inputs, trying every subset of every possible substitution could quickly get large,
        # but there might be a performant way to fix.
        # (so in this example: {'4': a, '0': 'o'} is detected as a possible sub,
        # but the subset {'4': 'a'} isn't tried, missing the match for asdf0.)
        # TODO: consider partially fixing by trying all subsets of size 1 and maybe 2
        $msg = 'doesn\'t match with subsets of possible l33t substitutions';
        $this->assertEquals([], $this->lm('4sdf0'), $msg);

    }

    private function lm($pw)
    {
        $dictionaryMatch = new DictionaryMatch();
        $dictionaryMatch->setRankedDictionaries($this->testDicts);

        $l33tMatch = new L33tMatch();
        $l33tMatch->setPassword($pw);
        $l33tMatch->setL33tTable($this->testTable);
        $l33tMatch->setDictionaryMatch($dictionaryMatch);

        return $l33tMatch->getMatches();
    }
}
