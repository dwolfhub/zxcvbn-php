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
     * @var array
     */
    protected $rankedDictionaries;

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

    /**
     * @return array
     */
    public function getRankedDictionaries()
    {
        if (empty($this->rankedDictionaries)) {
            $this->rankedDictionaries = [];
            foreach (FrequencyLists::getData() as $name => $lst) {
                $this->rankedDictionaries[$name] = $this->buildRankedDict($lst);
            }
        }

        return $this->rankedDictionaries;
    }

    /**
     * @param array $rankedDictionaries
     */
    public function setRankedDictionaries(array $rankedDictionaries)
    {
        $this->rankedDictionaries = $rankedDictionaries;
    }

    /**
     * @param array $orderedList
     * @return array
     */
    protected function buildRankedDict(array $orderedList)
    {
        $result = [];
        for ($i = 1; $i <= count($orderedList); $i++) {
            $result[$orderedList[$i - 1]] = $i;
        }
        return $result;
    }
}