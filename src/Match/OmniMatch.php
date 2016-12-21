<?php

namespace ZxcvbnPhp\Match;

class OmniMatch extends AbstractMatch
{
    public function getMatches()
    {
        $matches = [];
        $matchers = []; // todo

        foreach ($matchers as $matcher) {
            // todo
        }

        usort($matches, function ($a, $b) {
            // compare ['i'] then ['j']
            if ($a['i'] < $b['i']) {
                return -1;
            } else if ($a['i'] === $b['i']) {
                if ($a['j'] < $b['j']) {
                    return -1;
                } else if ($a['j'] === $b['j']) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1;
            }
        });

        return $matches;
    }
}