<?php

namespace Zxcvbn\Match;

/**
 * Class ReverseDictionaryMatch
 * @package Zxcvbn\Match
 */
class ReverseDictionaryMatch extends AbstractMatch
{
    /**
     * @var DictionaryMatch
     */
    protected $dictionaryMatch;

    /**
     * {@inheritdoc}
     */
    public function getMatches()
    {
        // todo refactor this
        $this->dictionaryMatch = new DictionaryMatch(strrev($this->password));

        $matches = $this->dictionaryMatch->getMatches();
        $processedMatches = [];
        foreach ($matches as $match) {
            $match['token'] = strrev($match['token']);
            $match['reversed'] = true;
            $match['i'] = strlen($this->password) - 1 - $match['j'];
            $match['j'] = strlen($this->password) - 1 - $match['i'];

            $processedMatches[] = $match;
        }

        usort($processedMatches, [$this, 'sortByIAndJ']);

        return $processedMatches;
    }
}