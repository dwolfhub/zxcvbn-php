<?php

namespace ZxcvbnPhp;

/**
 * Class FeedbackProvider
 * @package ZxcvbnPhp
 */
class FeedbackProvider
{
    /**
     * @var int
     */
    protected $score;

    /**
     * @var array
     */
    protected $sequence;


    /**
     * FeedbackProvider constructor.
     * @param $score
     * @param $sequence
     */
    public function __construct($score, $sequence)
    {
        $this->score = $score;
        $this->sequence = $sequence;
    }

    /**
     * @return array
     */
    public function getFeedback()
    {
        return [];
    }

    /**
     * @param array $match
     * @param $isSoleMatch
     * @return array
     */
    protected function getMatchFeedback(array $match, $isSoleMatch)
    {
        return [];
    }

    /**
     * @param array $match
     * @param $isSoleMatch
     * @return array
     */
    protected function getDictionaryMatchFeedback(array $match, $isSoleMatch)
    {
        $warning = '';
        if ($match['dictionary'] == 'passwords') {
            if ($isSoleMatch and !empty($match['l33t']) and !$match['reversed']) {
                if ($match['rank'] <= 10) {
                    $warning = 'This is a top-10 common password';
                } else if ($match['rank'] <= 100) {
                    $warning = 'This is a top-100 common password';
                } else {
                    $warning = 'This is a very common password';
                }
            } else if ($match['guesses_log10'] <= 4) {
                $warning = 'This is similar to a commonly used password';
            }
        } else if ($match['dictionary_name'] == 'english') {
            if ($isSoleMatch) {
                $warning = 'A word by itself is easy to guess';
            }
        } else if (in_array($match['dictionary_name'], ['surnames', 'male_names', 'female_names',])) {
            if ($isSoleMatch) {
                $warning = 'Names and surnames by themselves are easy to guess';
            } else {
                $warning = 'Common names and surnames are easy to guess';
            }
        } else {
            $warning = '';
        }

        $suggestions = [];
        $word = $match['token'];
        if (preg_match(Scoring::START_UPPER, $word)) {
            array_push($suggestions, "Capitalization doesn't help very much");
        } else if (preg_match(Scoring::ALL_UPPER, $word) and strtolower($word) != $word) {
            array_push($suggestions, 'All-uppercase is almost as easy to guess as all-lowercase');
        }

        if ($match['reversed'] and count($match['token']) >= 4) {
            array_push($suggestions, 'Reversed words aren\'t much harder to guess');
        }
        if (!empty($match['l33t'])) {
            array_push($suggestions, "Predictable substitutions like '@' instead of 'a' don't help very much");
        }

        return [
            'warning' => $warning,
            'suggestions' => $suggestions,
        ];
    }
}