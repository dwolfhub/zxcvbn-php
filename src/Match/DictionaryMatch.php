<?php

namespace ZxcvbnPhp\Match;

use ZxcvbnPhp\Match\DataProvider\FrequencyLists;

/**
 * Class DictionaryMatch
 * @package ZxcvbnPhp\Match
 */
class DictionaryMatch extends AbstractMatch
{
    /**
     * {@inheritdoc}
     */
    public function getMatches()
    {
        $matches = [];
        $length = strlen($this->password);
        $passwordLower = strtolower($this->password);
        foreach ($this->getRankedDictionaries() as $dictionaryName => $rankedDict) {
            for ($i = 0; $i < $length; $i++) {
                for ($j = $i; $j < $length; $j++) {
                    if (in_array(substr($passwordLower, $i, $j - $i), $rankedDict)) {
                        $word = substr($passwordLower, $i, $j - $i);
                        $rank = $rankedDict[$word];
                        array_push($matches, [
                            'pattern' => 'dictionary',
                            'i' => $i,
                            'j' => $j,
                            'token' => substr($this->password, $i, $j - $i),
                            'matched_word' => $word,
                            'rank' => $rank,
                            'dictionary_name' => $dictionaryName,
                            'reversed' => false,
                            'l33t' => false,
                        ]);
                    }
                }
            }
        }

        usort($matches, [$this, 'sortByIAndJ']);

        return $matches;
    }

}