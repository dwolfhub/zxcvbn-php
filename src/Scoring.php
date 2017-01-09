<?php

namespace Zxcvbn;

use InvalidArgumentException;
use Zxcvbn\Guess\AbstractEstimator;
use Zxcvbn\Guess\EstimatorFactory;

/**
 * Class Scoring
 * @package Zxcvbn
 */
class Scoring
{
    /**
     * @var string regular expression
     */
    const START_UPPER = '/^[A-Z][^A-Z]+$/';

    /**
     * @var string regular expression
     */
    const END_UPPER = '/^[^A-Z]+[A-Z]$/';

    /**
     * @var string regular expression
     */
    const ALL_UPPER = '/^[^a-z]+$/';

    /**
     * @var string regular expression
     */
    const ALL_LOWER = '/^[^A-Z]+$/';

    /**
     * @var int
     */
    const MIN_GUESSES_BEFORE_GROWING_SEQUENCE = 10000;

    /**
     * @var int
     */
    const MIN_SUBMATCH_GUESSES_SINGLE_CHAR = 10;

    /**
     * @var int
     */
    const MIN_SUBMATCH_GUESSES_MULTI_CHAR = 50;

    /**
     * @var int
     */
    const MIN_YEAR_SPACE = 20;

    /**
     * @var int
     */
    const REFERENCE_YEAR = 2017;

    /**
     * @param string $password
     * @param array $matches
     * @param bool $excludeAdditive
     * @return array
     */
    public function mostGuessableMatchSequence($password, $matches, $excludeAdditive = false)
    {
        $n = strlen($password);

        $optimal = [
            // optimal.m[k][l] holds final match in the best length-l match sequence
            // covering the password prefix up to k, inclusive.
            // if there is no length-l sequence that scores better (fewer guesses)
            // than a shorter match sequence spanning the same prefix,
            // optimal.m[k][l] is undefined.
            'm' => array_fill(0, $n, []),
            // same structure as optimal.m -- holds the product term Prod(m.guesses
            // for m in sequence). optimal.pi allows for fast (non-looping) updates
            // to the minimization function.
            'pi' => array_fill(0, $n, []),
            // same structure as optimal.m -- holds the overall metric.
            'g' => array_fill(0, $n, []),
        ];

        $n = strlen($password);

        $matchesByJ = array_fill(0, $n, []);
        foreach ($matches as $m) {
            array_push($matchesByJ[$m['j']], $m);
        }

        for ($i = 0; $i < count($matchesByJ); $i++) {
            usort($matchesByJ[$i], function ($a, $b) {
                if ($a['i'] < $b['i']) {
                    return -1;
                } else if ($a['i'] === $b['i']) {
                    return 0;
                } else {
                    return 1;
                }
            });
        }

        for ($k = 0; $k < $n; $k++) {
            foreach ($matchesByJ[$k] as $m) {
                if ($m['i'] > 0) {
                    foreach ($optimal['m'][$m['i'] - 1] as $l) {
                        $l = (int)$l;
                        $this->update($password, $optimal, $excludeAdditive, $m, $l + 1);
                    }
                } else {
                    $this->update($password, $optimal, $excludeAdditive, $m, 1);
                }
            }

            $this->bruteforceUpdate($password, $optimal, $excludeAdditive, $k);
        }

        $optimalMatchSequence = $this->unwind($optimal, $n);
        $optimalL = count($optimalMatchSequence);

        if ($n == 0) {
            $guesses = 1;
        } else {
            $guesses = $optimal['g'][$n - 1][$optimalL];
        }

        return [
            'password' => $password,
            'guesses' => $guesses,
            'guesses_log10' => log($guesses, 10),
            'sequence' => $optimalMatchSequence,
        ];
    }

    /**
     * helper: considers whether a length-l sequence ending at match m is better
     * (fewer guesses) than previously encountered sequences, updating state if
     * so.
     *
     * @param string $password
     * @param array $optimal
     * @param bool $excludeAdditive
     * @param $m
     * @param $l
     */
    protected function update($password, &$optimal, $excludeAdditive, $m, $l)
    {
        $k = $m['j'];
        $pi = $this->estimateGuesses($password, $m);
        if ($l > 1) {
            // we're considering a length-l sequence ending with match m:
            // obtain the product term in the minimization function by
            // multiplying m's guesses by the product of the length-(l-1)
            // sequence ending just before m, at m.i - 1.
            $pi *= $optimal['pi'][$m['i'] - 1][$l - 1];
        }
        // calculate the minimization func
        $g = gmp_fact($l) * $pi;
        if (!$excludeAdditive) {
            $g += self::MIN_GUESSES_BEFORE_GROWING_SEQUENCE ** ($l - 1);
        }

        // update state if new best.
        // first see if any competing sequences covering this prefix, with l or
        // fewer matches, fare better than this sequence. if so, skip it and
        // return.
        foreach ($optimal['g'][$k] as $competingL => $competingG) {
            if ($competingL > $l) {
                continue;
            }
            if ($competingG <= $g) {
                return;
            }
        }

        // this sequence might be part of the final optimal sequence.
        $optimal['g'][$k][$l] = $g;
        $optimal['m'][$k][$l] = $m;
        $optimal['pi'][$k][$l] = $pi;
    }

    /**
     * helper: evaluate bruteforce matches ending at k.
     *
     * @param string $password
     * @param array $optimal
     * @param bool $excludeAdditive
     * @param $k
     */
    protected function bruteforceUpdate($password, &$optimal, $excludeAdditive, $k)
    {
        // see if a single bruteforce match spanning the k-prefix is optimal.
        $m = $this->makeBruteforceMatch($password, 0, $k);
        $this->update($password, $optimal, $excludeAdditive, $m, 1);

        for ($i = 1; $i < $k; $i++) {
            // generate k bruteforce matches, spanning from (i=1, j=k) up to
            // (i=k, j=k). see if adding these new matches to any of the
            // sequences in optimal[i-1] leads to new bests.
            foreach ($optimal['m'][$i - 1] as $l => $lastM) {
                $l = (int)$l;

                // corner: an optimal sequence will never have two adjacent
                // bruteforce matches. it is strictly better to have a single
                // bruteforce match spanning the same region: same contribution
                // to the guess product with a lower length.
                // --> safe to skip those cases.
                if (!empty($lastM['pattern']) and $lastM['pattern'] === 'bruteforce') {
                    continue;
                }

                $this->update($password, $optimal, $excludeAdditive, $m, $l + 1);
            }
        }
    }

    /**
     * helper: make bruteforce match objects spanning i to j, inclusive.
     *
     * @param string $password
     * @param int $i
     * @param int $j
     * @return array
     */
    protected function makeBruteforceMatch($password, $i, $j)
    {
        return [
            'pattern' => 'bruteforce',
            'token' => substr($password, $i, $j - $i + 1),
            'i' => $i,
            'j' => $j,
        ];
    }

    /**
     * helper: step backwards through optimal.m starting at the end,
     * constructing the final optimal match sequence.
     *
     * @param array $optimal
     * @param int $n
     * @return array
     */
    protected function unwind(&$optimal, $n)
    {
        $optimalMatchSequence = [];
        $k = $n - 1;

        // find the final best sequence length and score
        $l = null;
        $g = INF;

        foreach ($optimal['g'][$k] as $candidateL => $candidateG) {
            if ($candidateG < $g) {
                $l = $candidateL;
                $g = $candidateG;
            }
        }

        while ($k >= 0) {
            $m = $optimal['m'][$k][$l];
            array_unshift($optimalMatchSequence, $m);
            $k = $m['i'] - 1;
            $l -= 1;
        }

        return $optimalMatchSequence;
    }

    /**
     * @param string $password
     * @param array $match
     * @return mixed
     */
    protected function estimateGuesses($password, &$match)
    {
        if (!empty($match['guesses'])) {
            return $match['guesses'];
        }

        $minGuesses = 1;
        if (strlen($match['token']) < strlen($password)) {
            if (strlen($match['token']) === 1) {
                $minGuesses = self::MIN_SUBMATCH_GUESSES_SINGLE_CHAR;
            } else {
                $minGuesses = self::MIN_SUBMATCH_GUESSES_MULTI_CHAR;
            }
        }

        $estimatorFactory = new EstimatorFactory();
        $estimationFunctions = [
            'bruteforce' => $estimatorFactory->create(EstimatorFactory::TYPE_BRUTE_FORCE),
            'dictionary' => $estimatorFactory->create(EstimatorFactory::TYPE_DICTIONARY),
            'spatial' => $estimatorFactory->create(EstimatorFactory::TYPE_SPATIAL),
            'repeat' => $estimatorFactory->create(EstimatorFactory::TYPE_REPEAT),
            'sequence' => $estimatorFactory->create(EstimatorFactory::TYPE_SEQUENCE),
            'regex' => $estimatorFactory->create(EstimatorFactory::TYPE_REGEX),
            'date' => $estimatorFactory->create(EstimatorFactory::TYPE_DATE),
        ];

        if (empty($estimationFunctions[$match['pattern']])) {
            throw new InvalidArgumentException(sprintf('Match pattern %s in invalid.', $match['pattern']));
        }

        $guesses = $estimationFunctions[$match['pattern']]->estimate($match);

        $match['guesses'] = max($guesses, $minGuesses);
        $match['guesses_log10'] = log($match['guesses'], 10);

        return $match['guesses'];
    }
}