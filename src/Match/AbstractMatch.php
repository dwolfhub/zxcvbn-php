<?php

namespace ZxcvbnPhp\Match;

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
    abstract public function getMatches();
}