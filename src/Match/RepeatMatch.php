<?php

namespace Zxcvbn\Match;

/**
 * repeats (aaa, abcabcabc) and sequences (abcdef)
 *
 * Class RepeatMatch
 * @package Zxcvbn\Match
 */
class RepeatMatch extends AbstractMatch
{
    public function getMatches()
    {
        $matches = [];
        $greedy = '/(.+)\1+/';
        $lazy = '/(.+?)\1+/';
        $lazyAnchored = '/^(.+?)\1+$/';
        $lastIndex = 0;

        while ($lastIndex < strlen($this->password)) {
            preg_match($greedy, $this->password, $greedyMatch, 0, $lastIndex);
            preg_match($lazy, $this->password, $lazyMatch, 0, $lastIndex);

            if (empty($greedyMatch)) {
                break;
            }

            if (strlen($greedyMatch[0]) > strlen($lazyMatch[0])) {
                // greedy beats lazy for 'aabaab'
                //   greedy: [aabaab, aab]
                //   lazy:   [aa,     a]
                $match = $greedyMatch;
                // greedy's repeated string might itself be repeated, eg.
                // aabaab in aabaabaabaab.
                // run an anchored lazy match on greedy's repeated string
                // to find the shortest repeated string
                preg_match()
                $baseToken = lazy_anchored.search(match.group(0)).group(1)
            }
        }
    }
}