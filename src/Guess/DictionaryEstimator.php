<?php
namespace Zxcvbn\Guess;

/**
 * Class DictionaryEstimator
 * @package Zxcvbn\Guess
 */
class DictionaryEstimator extends AbstractEstimator
{
    /**
     * @var string regular expression
     */
    const ALL_LOWER = '/^[^A-Z]+$/';

    /**
     * @var string regular expression
     */
    const ALL_UPPER = '/^[^a-z]+$/';

    /**
     * @var string regular expression
     */
    const END_UPPER = '/^[^A-Z]+[A-Z]$/';

    /**
     * @var string regular expression
     */
    const START_UPPER = '/^[A-Z][^A-Z]+$/';

    /**
     * {@inheritdoc}
     */
    public function estimate($match)
    {
        $match['base_guesses'] = $match['rank'];
        $match['uppercase_variations'] = $this->uppercaseVariations($match);
        $match['l33t_variations'] = $this->l33tVariations($match);

        $reversedVariations = !empty($match['reversed']) ? 2 : 1;

        return $match['base_guesses']
            * $match['uppercase_variations']
            * $match['l33t_variations']
            * $reversedVariations;
    }

    /**
     * @param array $match
     * @return int
     */
    protected function uppercaseVariations($match)
    {
        $word = $match['token'];

        if (preg_match(self::ALL_LOWER, $word) or strtolower($word) == $word) {
            return 1;
        }

        foreach ([self::START_UPPER, self::END_UPPER, self::ALL_UPPER] as $regex) {
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
     * @var array $match
     * @return int
     */
    protected function l33tVariations($match)
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
                $p = min($u, $s);
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