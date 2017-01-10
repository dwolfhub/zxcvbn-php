<?php
namespace Zxcvbn\Guess;

use Zxcvbn\Match\DataProvider\AdjacencyGraphs;

/**
 * Class SpatialEstimator
 * @package Zxcvbn\Guess
 */
class SpatialEstimator extends AbstractEstimator
{

    /**
     * {@inheritdoc}
     */
    public function estimate($match)
    {
        // @todo decouple?
        $adjacencyGraphs = AdjacencyGraphs::getData();

        if (in_array($match['graph'], ['qwerty', 'dvorak'])) {
            $s = count($adjacencyGraphs['qwerty']);
            $d = $this->calcAverageDegree($adjacencyGraphs['qwerty']);
        } else {
            $s = count($adjacencyGraphs['keypad']);
            $d = $this->calcAverageDegree($adjacencyGraphs['keypad']);
        }

        $guesses = 0;
        $L = strlen($match['token']);
        $t = $match['turns'];

        // estimate the number of possible patterns w/ length L or less with t turns
        // or less.
        for ($i = 2; $i <= $L; $i++) {
            $possibleTurns = min($t, $i - 1);
            for ($j = 1; $j <= $possibleTurns; $j++) {
                $guesses += $this->nCK($i - 1, $j - 1) * $s * ($d ** $j);
            }
        }
        // add extra guesses for shifted keys. (% instead of 5, A instead of a.)
        // math is similar to extra guesses of l33t substitutions in dictionary
        // matches.
        if ($match['shifted_count']) {
            $S = $match['shifted_count'];
            $U = strlen($match['token']) - $match['shifted_count']; // unshifted count
            if ($S === 0 or $U === 0) {
                $guesses *= 2;
            } else {
                $shiftedVariations = 0;
                for ($i = 1; $i <= min($S, $U); $i++) {
                    $shiftedVariations += $this->nCK($S + $U, $i);
                }
                $guesses *= $shiftedVariations;
            }
        }

        return $guesses;
    }

    /**
     * @param $graph
     * @return float|int
     */
    protected function calcAverageDegree($graph)
    {
        $average = 0;

        foreach ($graph as $key => $neighbors) {
            $average += array_reduce($neighbors, function ($carry, $item) {
                if ($item) {
                    $carry++;
                }

                return $carry;
            }, 0);
        }

        return $average / count($graph);
    }
}