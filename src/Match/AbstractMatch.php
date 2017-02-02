<?php

namespace Zxcvbn\Match;

/**
 * Class AbstractMatch
 * @package Match
 */
abstract class AbstractMatch implements MatchInterface
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
     * usort function, compares 'i' then 'j' indexes
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function sortByIAndJ($a, $b)
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
     * @param array $dictionaries
     */
    public function setRankedDictionaries(array $dictionaries)
    {
        $this->rankedDictionaries = [];
        foreach ($dictionaries as $name => $lst) {
            if (is_string($lst) === true) {
                $lst = explode(',', $lst);
            }
            $this->rankedDictionaries[$name] = $this->buildRankedDict($lst);
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
     * @param $name string
     * @param $dictionary array
     */
    public function addRankedDictionary($name, $dictionary)
    {
        $this->rankedDictionaries[$name] = $this->buildRankedDict($dictionary);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getMatches();
}