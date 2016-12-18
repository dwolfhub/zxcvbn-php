<?php

namespace ZxcvbnPhp;

/**
 * Class Scoring
 * @package ZxcvbnPhp
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
    const REFERENCE_YEAR = 2016;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var array
     */
    protected $optimal;

    /**
     * @var bool
     */
    protected $excludeAdditive;

    public function __construct($password, $excludeAdditive = false)
    {
        $this->password = $password;
        $this->excludeAdditive = $excludeAdditive;

        $passwordLen = strlen($password);
        $this->optimal = [
            // optimal.m[k][l] holds final match in the best length-l match sequence
            // covering the password prefix up to k, inclusive.
            // if there is no length-l sequence that scores better (fewer guesses)
            // than a shorter match sequence spanning the same prefix,
            // optimal.m[k][l] is undefined.
            'm' => array_fill(0, $passwordLen, []),
            // same structure as optimal.m -- holds the product term Prod(m.guesses
            // for m in sequence). optimal.pi allows for fast (non-looping) updates
            // to the minimization function.
            'pi' => array_fill(0, $passwordLen, []),
            // same structure as optimal.m -- holds the overall metric.
            'g' => array_fill(0, $passwordLen, []),
        ];


    }

    /**
     * @param $matches
     * @return array
     */
    public function mostGuessableMatchSequence($matches)
    {
        $n = strlen($this->password);

        $matchesByJ = array_fill(0, $n, []);
        foreach ($matches as $m) {
            array_push($matchesByJ[$m['j']], $m);
        }

        for ($i = 0; $i < count($matchesByJ); $i++) {
            usort($matchesByJ[$i], function ($a, $b) {
                return $a['i'] < $b['i'];
            });
        }

        for ($k = 0; $k < $n; $k++) {
            foreach ($matchesByJ[$k] as $m) {
                if ($m['i'] > 0) {
                    foreach ($this->optimal['m'][$m['i'] - 1] as $l) {
                        $l = (int)$l;
                        $this->update($m, $l + 1);
                    }
                } else {
                    $this->update($m, 1);
                }
            }

            $this->bruteforceUpdate($k);
        }

        $optimalMatchSequence = $this->unwind($n);
        $optimalL = count($optimalMatchSequence);

        if ($n == 0) {
            $guesses = 1;
        } else {
            $guesses = $this->optimal['g'][$n - 1][$optimalL];
        }

        return [
            'password' => $this->password,
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
     * @param $m
     * @param $l
     */
    protected function update($m, $l)
    {
        $k = $m['j'];
        $pi = $this->estimateGuesses($m);
        if ($l > 1) {
            // we're considering a length-l sequence ending with match m:
            // obtain the product term in the minimization function by
            // multiplying m's guesses by the product of the length-(l-1)
            // sequence ending just before m, at m.i - 1.
            $pi *= $this->optimal['pi'][$m['i'] - 1][$l - 1];
        }
        // calculate the minimization func
        $g = gmp_fact($l) * $pi;
        if (!$this->excludeAdditive) {
            $g += self::MIN_GUESSES_BEFORE_GROWING_SEQUENCE ** ($l - 1);
        }

        // update state if new best.
        // first see if any competing sequences covering this prefix, with l or
        // fewer matches, fare better than this sequence. if so, skip it and
        // return.
        foreach ($this->optimal['g'][$k] as $competingL => $competingG) {
            if ($competingL > $l) {
                continue;
            }
            if ($competingG <= $g) {
                return;
            }
        }

        // this sequence might be part of the final optimal sequence.
        $this->optimal['g'][$k][$l] = $g;
        $this->optimal['m'][$k][$l] = $m;
        $this->optimal['pi'][$k][$l] = $pi;
    }

    /**
     * helper: evaluate bruteforce matches ending at k.
     *
     * @param $k
     */
    protected function bruteforceUpdate($k)
    {
        // see if a single bruteforce match spanning the k-prefix is optimal.
        $m = $this->makeBruteforceMatch(0, $k);
        $this->update($m, 1);

        for ($i = 1; $i < $k; $i++) {
            // generate k bruteforce matches, spanning from (i=1, j=k) up to
            // (i=k, j=k). see if adding these new matches to any of the
            // sequences in optimal[i-1] leads to new bests.
            foreach ($this->optimal['m'][$i - 1] as $l => $lastM) {
                $l = (int)$l;

                // corner: an optimal sequence will never have two adjacent
                // bruteforce matches. it is strictly better to have a single
                // bruteforce match spanning the same region: same contribution
                // to the guess product with a lower length.
                // --> safe to skip those cases.
                if (!empty($lastM['pattern']) and $lastM['pattern'] === 'bruteforce') {
                    continue;
                }

                $this->update($m, $l + 1);
            }
        }
    }

    /**
     * helper: make bruteforce match objects spanning i to j, inclusive.
     *
     * @param $i
     * @param $j
     * @return array
     */
    protected function makeBruteforceMatch($i, $j)
    {
        return [
            'pattern' => 'bruteforce',
            'token' => substr($this->password, $i, $j - $i + 1),
            'i' => $i,
            'j' => $j,
        ];
    }

    /**
     * helper: step backwards through optimal.m starting at the end,
     * constructing the final optimal match sequence.
     *
     * @param $n
     * @return array
     */
    protected function unwind($n)
    {
        $optimalMatchSequence = [];
        $k = $n - 1;

        // find the final best sequence length and score
        $l = null;
        $g = INF;

        foreach ($this->optimal['g'][$k] as $candidateL => $candidateG) {
            if ($candidateG < $g) {
                $l = $candidateL;
                $g = $candidateG;
            }
        }

        while ($k >= 0 ) {
            $m = $this->optimal['m'][$k][$l];
            array_unshift($optimalMatchSequence, $m);
            $k = $m['i'] - 1;
            $l -= 1;
        }

        return $optimalMatchSequence;
    }

    /**
     * @param $match
     */
    protected function estimateGuesses($match)
    {
        if (!empty($match['guesses'])) {
            return $match['guesses'];
        }

        $minGuesses = 1;
        if (strlen($match['token']) < strlen($this->password)) {
            if (strlen($match['token']) ===1 ) {
                $minGuesses = self::MIN_SUBMATCH_GUESSES_SINGLE_CHAR;
            } else {
                $minGuesses = self::MIN_SUBMATCH_GUESSES_MULTI_CHAR;
            }
        }

        $estimationFunctions = [
            // todo
        ];

        $guesses = $estimationFunctions[$match['pattern']]->match();
        // todo
    }
}