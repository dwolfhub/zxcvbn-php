<?php

namespace Zxcvbn\Match;

/**
 * Class DictionaryMatch
 * @package Zxcvbn\Match
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
                    if (array_key_exists(substr($passwordLower, $i, $j - $i + 1), $rankedDict)) {
                        $word = substr($passwordLower, $i, $j - $i + 1);
                        $rank = $rankedDict[$word];
                        $matches[] = [
                            'pattern' => 'dictionary',
                            'i' => $i,
                            'j' => $j,
                            'token' => substr($this->password, $i, $j - $i + 1),
                            'matched_word' => $word,
                            'rank' => $rank,
                            'dictionary_name' => $dictionaryName,
                            'reversed' => false,
                            'l33t' => false,
                        ];
                    }
                }
            }
        }

        usort($matches, [$this, 'sortByIAndJ']);

        return $matches;
    }

}