<?php
namespace Zxcvbn\Guess;

/**
 * Class Estimator
 * @package Zxcvbn\Guess
 */
abstract class AbstractEstimator implements EstimatorInterface
{
    /**
     * @var int
     */
    const MIN_YEAR_SPACE = 20;

    /**
     * @return int
     */
    abstract public function estimate($match);

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
        for ($d = 1; $d <= $k; $d++) {
            $r *= $n;
            $r /= $d;
            $n -= 1;
        }

        return $r;
    }
}