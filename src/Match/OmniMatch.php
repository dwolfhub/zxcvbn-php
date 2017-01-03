<?php

namespace Zxcvbn\Match;

class OmniMatch extends AbstractMatch
{
    public function getMatches()
    {
        $matches = [];
        $matchers = []; // todo

        foreach ($matchers as $matcher) {
            // todo
        }

        usort($matches, [$this, '']);

        return $matches;
    }
}