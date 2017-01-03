<?php

namespace Zxcvbn\Match;

/**
 * Class RegexMatch
 * @package Zxcvbn\Match
 */
class RegexMatch extends AbstractMatch
{
    /**
     * @var array
     */
    protected $regexen;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        // todo refactor this
        $this->regexen = [
            'recent_year' => '/19\d\d|200\d|201\d/',
        ];
    }

    /**
     * @return array
     */
    public function getMatches()
    {
        $matches = [];
        foreach ($this->regexen as $name => $regex) {
            $rxMatch = preg_match($regex, $this->password, $matches);
            if ($rxMatch) {
                $token = $matches[0];
                $startPos = strpos($token, $this->password);
                array_push($matches, [
                    'pattern' => 'regex',
                    'token' => $token,
                    'i' => $startPos,
                    'j' => $startPos + strlen($token) - 1,
                    'regex_name' => $name,
                    'regex_match' => $rxMatch,
                ]);
            }
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