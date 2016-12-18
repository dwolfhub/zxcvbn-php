<?php

namespace ZxcvbnPhp\Guess;

/**
 * Class Estimator
 * @package ZxcvbnPhp\Guess
 */
abstract class AbstractEstimator
{
    /**
     * @var array
     */
    protected $match;

    /**
     * Estimator constructor.
     * @param $match
     */
    public function __construct($match)
    {
        $this->match = $match;
    }

    /**
     * @param $n
     * @param $k
     * @return int
     */
    protected function nCK($n, $k)
    {
        // http://blog.plover.com/math/choose.html
        if ($k > $n) {
            return 0;
        }
        if ($k == 0) {
            return 1;
        }

        $r = 1;
        for ($d = 1; $d < $k + 1; $d++) {
            $r *= $n;
            $r /= $d;
            $n -= 1;
        }

        return $r;
    }

    /**
     * @return int
     */
    abstract public function estimate();
}