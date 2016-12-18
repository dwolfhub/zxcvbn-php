<?php
namespace ZxcvbnPhp\Guess;

use ZxcvbnPhp\Scoring;

/**
 * Class DictionaryEstimator
 * @package ZxcvbnPhp\Guess
 */
class DictionaryEstimator extends AbstractEstimator
{
    /**
     * {@inheritdoc}
     */
    public function estimate()
    {
        $this->match['base_guesses'] = $this->match['rank'];
        $this->match['uppercase_variations'] = $this->uppercaseVariations();
        $this->match['l33t_variations'] = $this->l33tVariations();

        $reversedVariations = !empty($match['reversed']) ? 2 : 1;

        return $this->match['base_guesses']
            * $this->match['uppercase_variations']
            * $this->match['l33t_variations']
            * $reversedVariations;
    }

    /**
     * @return int
     */
    protected function uppercaseVariations()
    {
        $word = $this->match['token'];

        if (preg_match(Scoring::ALL_LOWER, $word) or strtolower($word) == $word) {
            return 1;
        }

        foreach ([Scoring::START_UPPER, Scoring::END_UPPER, Scoring::ALL_UPPER] as $regex) {
            if (preg_match($regex, $word)) {
                return 2;
            }
        }

        $u = strlen(preg_replace('![^A-Z]+!', '', $word));
        $l = strlen(preg_replace('![^a-z]+!', '', $word));
        $variations = 0;

        for ($i = 0; $i <= min($u, $l); $i++) {
            $variations += $this->nCK($u + $l, $i);
        }

        return $variations;
    }

    /**
     *
     */
    protected function l33tVariations()
    {
        if (empty($match['l33t'])) {
            return 1;
        }

        $variations = 1;

        foreach ($match['sub'] as $subbed => $unsubbed) {
            // lower-case match.token before calculating: capitalization shouldn't
            // affect l33t calc.
            $chrs = str_split(strtolower($match['token']));
            $s = array_reduce($chrs, function ($carry, $item) use ($subbed) {
                if ($item === $subbed) {
                    $carry++;
                }

                return $carry;
            }, 0);
            $u = array_reduce($chrs, function ($carry, $item) use ($unsubbed) {
                if ($item === $unsubbed) {
                    $carry++;
                }

                return $carry;
            }, 0);

            if ($s === 0 or $u === 0) {
                // for this sub, password is either fully subbed (444) or fully
                // unsubbed (aaa) treat that as doubling the space (attacker needs
                // to try fully subbed chars in addition to unsubbed.)
                $variations *= 2;
            } else {
                // this case is similar to capitalization:
                // with aa44a, U = 3, S = 2, attacker needs to try unsubbed + one
                // sub + two subs
                $possibilities = 0;
                for ($i = 1; $i <= $p; $i++) {
                    $possibilities += $this->nCK($u + $s, $i);
                }
                $variations *= $possibilities;
            }
        }

        return $variations;
    }
}