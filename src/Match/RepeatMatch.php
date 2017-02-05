<?php

namespace Zxcvbn\Match;

use Zxcvbn\Scoring;

/**
 * repeats (aaa, abcabcabc) and sequences (abcdef)
 *
 * Class RepeatMatch
 * @package Zxcvbn\Match
 */
class RepeatMatch extends AbstractMatch
{
    /**
     * @var Scoring
     */
    protected $scoring;

    /**
     * @var AbstractMatch
     */
    protected $omniMatch;

    /**
     * @return array
     */
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
                preg_match($lazyAnchored, $match[0], $lazyAnchoredMatch);
                $baseToken = $lazyAnchoredMatch[1];
            } else {
                $match = $lazyMatch;
                $baseToken = $match[1];
            }

            $i = strpos($this->password, $match[0]);
            $j = $i + strlen($match[0]) - 1;

            // recursively match and score the base string
            $this->omniMatch->setPassword($baseToken);
            $baseAnalysis = $this->scoring->mostGuessableMatchSequence(
                $baseToken,
                $this->omniMatch->getMatches()
            );
            $baseMatches = $baseAnalysis['sequence'];
            $baseGuesses = $baseAnalysis['guesses'];
            $matches[] = [
                'pattern' => 'repeat',
                'i' => $i,
                'j' => $j,
                'token' => $match[0],
                'base_token' => $baseToken,
                'base_guesses' => $baseGuesses,
                'base_matches' => $baseMatches,
                'repeat_count' => strlen($match[0]) / strlen($baseToken),
            ];

            $lastIndex = $j + 1;
        }

        return $matches;
    }

    /**
     * @return Scoring
     */
    public function getScoring()
    {
        return $this->scoring;
    }

    /**
     * @param Scoring $scoring
     */
    public function setScoring(Scoring $scoring)
    {
        $this->scoring = $scoring;
    }

    /**
     * @return AbstractMatch
     */
    public function getOmniMatch()
    {
        return $this->omniMatch;
    }

    /**
     * @param AbstractMatch $omniMatch
     */
    public function setOmniMatch(AbstractMatch $omniMatch)
    {
        $this->omniMatch = $omniMatch;
    }

}