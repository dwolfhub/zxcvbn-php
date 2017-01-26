<?php

namespace Zxcvbn\Match;

/**
 * Class RegexMatch
 * @package Zxcvbn\Match
 */
class RegexMatch extends AbstractMatch
{
    /**
     * @return array
     */
    public function getMatches()
    {
        $matches = [];
        preg_match('/19\d\d|200\d|201\d/', $this->password, $rxMatch);
        if ($rxMatch) {
            $token = $rxMatch[0];
            $startPos = strpos($this->password, $token);
            array_push($matches, [
                'pattern' => 'regex',
                'token' => $token,
                'i' => $startPos,
                'j' => $startPos + strlen($token) - 1,
                'regex_name' => 'recent_year',
                'regex_match' => $rxMatch,
            ]);
        }

        usort($matches, [$this, 'sortByIAndJ']);

        return $matches;
    }
}