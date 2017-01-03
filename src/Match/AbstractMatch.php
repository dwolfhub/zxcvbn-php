<?php

namespace Zxcvbn\Match;

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
     * AbstractMatch constructor.
     * @param $password
     */
    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * @return int
     */
    protected function sortByIAndJ($a, $b)
    {
        // compare ['i'] then ['j']
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