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
        $rxMatch = preg_match('/19\d\d|200\d|201\d/', $this->password, $matches);
        if ($rxMatch) {
            $token = $matches[0];
            $startPos = strpos($token, $this->password);
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

    /**
     * @return string
     */
    public function getRegexen()
    {
        return $this->regexen;
    }

    /**
     * @param string $regex
     */
    public function setRegexen($regex)
    {
        $this->regexen = $regex;
    }
}