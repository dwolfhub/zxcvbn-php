<?php

namespace Zxcvbn\Match;

/**
 * Class RegexMatch
 * @package Zxcvbn\Match
 */
class RegexMatch extends AbstractMatch
{
    const REGEXEN = [
        'recent_year' => '/19\d\d|200\d|201\d/'
    ];

    /**
     * @return array
     */
    public function getMatches()
    {
        $matches = [];
        foreach (self::REGEXEN as $regexName => $regex) {
            preg_match($regex, $this->password, $rxMatch);
            if ($rxMatch) {
                $token = $rxMatch[0];
                $startPos = strpos($this->password, $token);
                $matches[] = [
                    'pattern' => 'regex',
                    'token' => $token,
                    'i' => $startPos,
                    'j' => $startPos + strlen($token) - 1,
                    'regex_name' => $regexName,
                    'regex_match' => $rxMatch,
                ];
            }
        }

        usort($matches, [$this, 'sortByIAndJ']);

        return $matches;
    }
}