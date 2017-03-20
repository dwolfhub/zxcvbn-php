<?php
namespace test\functional\Match;

use PHPUnit\Framework\TestCase;

class AbstractFunctionalMatchTestCase extends TestCase
{
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
}