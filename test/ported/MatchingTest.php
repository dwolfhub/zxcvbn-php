<?php
namespace Zxcvbn;

class MatchingTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchingUtils()
    {
        $chrMap = [
            'a' => 'A',
            'b' => 'B',
        ];

        $stringMapResults = [
            ['a', $chrMap, 'A'],
            ['c', $chrMap, 'c'],
            ['ab', $chrMap, 'AB'],
            ['abc', $chrMap, 'ABc'],
            ['aa', $chrMap, 'AA'],
            ['abab', $chrMap, 'ABAB'],
            ['', $chrMap, ''],
            ['', [], ''],
            ['abc', [], 'abc'],
        ];

        foreach ($stringMapResults as $stringMapResult) {
            $string = $stringMapResult[0];
            $map = $stringMapResult[1];
            $result = $stringMapResult[2];

            $this->assertEquals()
        }
    }


    protected function checkMatches($prefix, $matches, $patternNames, $patterns, $ijs, $props)
    {
        if (is_string($patternNames)) {
            // shortcut: if checking for a list of the same type of patterns,
            // allow passing a string 'pat' instead of array ['pat', 'pat', ...]
            $patternNames = array_fill(0, count($patterns), $patternNames);
        }

        $isEqualLenArgs = [];
        $isEqualLenArgs[] = count($patternNames) === count($patterns);
        $isEqualLenArgs[] = count($patternNames) === count($ijs);
        foreach ($props as $prop => $lst) {
            // props is structured as: keys that points to list of values
            if (!$isEqualLenArgs[0] or !$isEqualLenArgs[1] or count($lst) !== count($patterns)) {
                throw new \Exception('unequal argument lists to check_matches');
            }
        }

        $msg = sprintf("%s: count(\$matches) == %s", $prefix, count($patterns));
        $this->assertEquals(count($matches), count($patterns), $msg);
        for ($k = 0; $k < count($patterns); $i++) {
            $match = $matches[$k];
            $patternName = $patternNames[$k];
            $pattern = $patterns[$k];
            $i = $ijs[$k][0];
            $j = $ijs[$k][1];
            $msg = sprintf("%s: \$matches[%s]['pattern'] == '%s'", $prefix, $k, $patternName);
            $this->assertEquals($match['pattern'], $patternName, $msg);

            $msg = sprintf("%s: \$matches[%s] should have [\$i, \$j] of [%s, %s]", $prefix, $k, $i, $j);
            $this->assertEquals([$match['i'], $match['j']], [$i, $j], $msg);

            $msg = sprintf("%s: \$matches[%s]['token'] == '%s'", $prefix, $k, $pattern);
            $this->assertEquals($match['token'], $pattern, $msg);

            foreach ($props as $propName => $propList) {
                $propMsg = $propList[$k];
                if (is_string($propMsg)) {
                    $propMsg = sprintf("'%s'", $propMsg);
                }
                $msg = sprintf("%s: \$matches[%s].%s == %s", $prefix, $k, $propName, $propMsg);
                $this->assertEquals($match[$propName], $propList[$k], $msg);
            }
        }
    }

    /**
     * takes a pattern and list of prefixes/suffixes
     * returns a bunch of variants of that pattern embedded
     * with each possible prefix/suffix combination, including no prefix/suffix
     * returns a list of triplets [variant, i, j] where [i,j] is the start/end of the
     * pattern, inclusive
     *
     * @param $pattern
     * @param $prefixes
     * @param $suffixes
     * @return array
     */
    protected function genPws($pattern, $prefixes, $suffixes)
    {
        if (!in_array('', $prefixes)) {
            array_unshift($prefixes, '');
        }
        if (!in_array('', $prefixes)) {
            array_unshift($prefixes, '');
        }

        $result = [];
        foreach ($prefixes as $prefix) {
            foreach ($suffixes as $suffix) {
                $i = count($prefix);
                $j = count($prefix) + strlen($pattern) - 1;
                array_push($result, [$prefix . $pattern . $suffix, $i, $j]);
            }
        }

        return $result;
    }
}