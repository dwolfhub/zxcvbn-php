<?php

namespace Zxcvbn;

use Zxcvbn\Guess\DictionaryEstimator;

/**
 * Class Feedback
 * @package Zxcvbn
 */
class Feedback
{
    /**
     * @var array
     */
    const DEFAULT_FEEDBACK = [
        'warning' => '',
        'suggestions' => [
            'Use a few words, avoid common phrases',
            'No need for symbols, digits, or uppercase letters'
        ]
    ];

    /**
     * @param int $score
     * @param array $sequence
     * @return array
     */
    public function getFeedback($score, $sequence)
    {
        if (count($sequence) === 0) {
            return self::DEFAULT_FEEDBACK;
        }

        if ($score > 2) {
            return [
                'warning' => '',
                'suggestions' => [],
            ];
        }

        $longestMatch = $sequence[0];
        foreach (array_slice($sequence, 1) as $match) {
            if (strlen($match['token']) > strlen($longestMatch['token'])) {
                $longestMatch = $match;
            }
        }

        $feedback = $this->getMatchFeedback($longestMatch, count($sequence) === 1);

        array_unshift($feedback['suggestions'], 'Add another word or two. Uncommon words are better.');

        if (empty($feedback['warning'])) {
            $feedback['warning'] = '';
        }

        return $feedback;
    }

    /**
     * @param array $match
     * @param $isSoleMatch
     * @return array
     */
    protected function getMatchFeedback(array $match, $isSoleMatch)
    {
        if ($match['pattern'] === 'dictionary') {
            return $this->getDictionaryMatchFeedback($match, $isSoleMatch);
        } else if ($match['pattern'] === 'spatial') {
            if ($match['turns'] === 1) {
                $warning = 'Straight rows of keys are easy to guess';
            } else {
                $warning = 'Short keyboard patterns are easy to guess';
            }

            return [
                'warning' => $warning,
                'suggestions' => [
                    'Use a longer keyboard pattern with more turns',
                ]
            ];
        } else if ($match['pattern'] === 'repeat') {
            if (strlen($match['base_token']) === 1) {
                $warning = 'Repeats like "aaa" are easy to guess';

            } else {
                $warning = 'Repeats like "abcabcabc" are only slightly harder to guess than "abc"';
            }

            return [
                'warning' => $warning,
                'suggestions' => [
                    'Avoid repeated words and characters'
                ],
            ];
        } else if ($match['pattern'] == 'sequence') {
            return [
                'warning' => 'Sequences like abc or 6543 are easy to guess',
                'suggestions' => [
                    'Avoid sequences'
                ],
            ];
        } else if ($match['pattern'] == 'regex') {
            if ($match['regex_name'] === 'recent_year') {
                return [
                    'warning' => 'Recent years are easy to guess',
                    'suggestions' => [
                        'Avoid recent years',
                        'Avoid years that are associated with you',
                    ]
                ];
            }
        } else if ($match['pattern'] == 'date') {
            return [
                'warning' => 'Dates are often easy to guess',
                'suggestions' => [
                    'Avoid dates and years that are associated with you',
                ]
            ];
        }

        return [
            'warning' => '',
            'suggestions' => [],
        ];
    }

    /**
     * @param array $match
     * @param $isSoleMatch
     * @return array
     */
    protected function getDictionaryMatchFeedback(array $match, $isSoleMatch)
    {
        $warning = '';
        if ($match['dictionary_name'] === 'passwords') {
            if ($isSoleMatch and empty($match['l33t']) and !$match['reversed']) {
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
        } else {
            if ($match['dictionary_name'] === 'english') {
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
        }

        $suggestions = [];
        $word = $match['token'];
        if (preg_match(DictionaryEstimator::START_UPPER, $word)) {
            array_push($suggestions, "Capitalization doesn't help very much");
        } else if (preg_match(DictionaryEstimator::ALL_UPPER, $word) and strtolower($word) != $word) {
            array_push($suggestions, 'All-uppercase is almost as easy to guess as all-lowercase');
        }

        if ($match['reversed'] and strlen($match['token']) >= 4) {
            array_push($suggestions, "Reversed words aren't much harder to guess");
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