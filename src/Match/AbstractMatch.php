<?php

namespace Zxcvbn\Match;

use Zxcvbn\Match\DataProvider\FrequencyLists;

/**
 * Class AbstractMatch
 * @package Match
 */
abstract class AbstractMatch
{
    /**
     * @var string
     */
    protected $password;

    /**
     * @var array
     */
    protected $rankedDictionaries;

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return array
     */
    public function getRankedDictionaries()
    {
        return $this->rankedDictionaries;
    }

    /**
     * @param array $rankedDictionaries
     */
    public function setRankedDictionaries(array $dictionaries)
    {
        $this->rankedDictionaries = [];
        foreach ($dictionaries as $name => $lst) {
            $this->rankedDictionaries[$name] = $this->buildRankedDict($lst);
        }
    }

    /**
     * @param $name string
     * @param $dictionary array
     */
    public function addRankedDictionary($name, $dictionary)
    {
        $this->rankedDictionaries[$name] = $this->buildRankedDict($dictionary);
    }

    /**
     * usort function, compares 'i' then 'j' indexes
     * @param array $a
     * @param array $b
     * @return int
     */
    protected static function sortByIAndJ($a, $b)
    {
        if ($a['i'] < $b['i']) {
            return -1;
        } else if ($a['i'] === $b['i']) {
            if ($a['j'] < $b['j']) {
                return -1;
            } else if ($a['j'] === $b['j']) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 1;
        }
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

    /**
     * @return array
     */
    abstract public function getMatches();
}