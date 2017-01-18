<?php

namespace Zxcvbn\Match;

use Zxcvbn\Scoring;

/**
 *
 * a "date" is recognized as:
 * any 3-tuple that starts or ends with a 2- or 4-digit year,
 * with 2 or 0 separator chars (1.1.91 or 1191),
 * maybe zero-padded (01-01-91 vs 1-1-91),
 * a month between 1 and 12,
 * a day between 1 and 31.
 *
 * note: this isn't true date parsing in that "feb 31st" is allowed,
 * this doesn't check for leap years, etc.
 *
 * recipe:
 * start with regex to find maybe-dates, then attempt to map the integers
 * onto month-day-year to filter the maybe-dates into dates.
 * finally, remove matches that are substrings of other matches to reduce noise.
 * note: instead of using a lazy or greedy regex to find many dates over the full string,
 * this uses a ^...$ regex against every substring of the password -- less performant but leads
 * to every possible date match.
 *
 * Class DateMatch
 * @package Match
 */
class DateMatch extends AbstractMatch
{
    /**
     * @var int
     */
    const DATE_MAX_YEAR = 2050;

    /**
     * @var int
     */
    const DATE_MIN_YEAR = 1000;

    /**
     * @var array
     */
    const DATE_SPLITS = [
        4 => [  # for length-4 strings, eg 1191 or 9111, two ways to split:
            [1, 2],  # 1 1 91 (2nd split starts at index 1, 3rd at index 2)
            [2, 3],  # 91 1 1
        ],
        5 => [
            [1, 3],  # 1 11 91
            [2, 3],  # 11 1 91
        ],
        6 => [
            [1, 2],  # 1 1 1991
            [2, 4],  # 11 11 91
            [4, 5],  # 1991 1 1
        ],
        7 => [
            [1, 3],  # 1 11 1991
            [2, 3],  # 11 1 1991
            [4, 5],  # 1991 1 11
            [4, 6],  # 1991 11 1
        ],
        8 => [
            [2, 4],  # 11 11 1991
            [4, 6],  # 1991 11 11
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getMatches()
    {
        $matches = [];
        $maybeDateNoSeparator = '/^\d{4,8}$/';
        $maybeDateWithSeparator = '/^(\d{1,4})([\s\/\\_.-])(\d{1,2})\2(\d{1,4})$/';
        $passwordLength = strlen($this->password);

        // dates without separators are between length 4 '1191' and 8 '11111991'
        for ($i = 0; $i < $passwordLength - 3; $i++) {
            for ($j = $i + 3; $j < $i + 8; $j++) {
                if ($j >= $passwordLength) {
                    break;
                }

                $token = substr($this->password, $i, $j - $i + 1);
                if (!preg_match($maybeDateNoSeparator, $token)) {
                    continue;
                }

                $candidates = [];
                foreach (self::DATE_SPLITS[strlen($token)] as list($k, $l)) {
                    $dmy = $this->mapIntsToDMY([
                        (int)substr($token, 0, $k),
                        (int)substr($token, $k, $l - $k),
                        (int)substr($token, $l),
                    ]);
                    if ($dmy) {
                        array_push($candidates, $dmy);
                    }
                }
                if (empty($candidates)) {
                    continue;
                }

                // at this point: different possible dmy mappings for the same i,j
                // substring. match the candidate date that likely takes the fewest
                // guesses: a year closest to 2000. (scoring.REFERENCE_YEAR).

                // ie, considering '111504', prefer 11-15-04 to 1-1-1504
                // (interpreting '04' as 2004)
                $bestCandidate = $candidates[0];
                $minDistance = $this->metric($bestCandidate);

                foreach (array_slice($candidates, 1) as $candidate) {
                    $distance = $this->metric($candidate);
                    if ($distance < $minDistance) {
                        $bestCandidate = $candidate;
                        $minDistance = $distance;
                    }
                }

                array_push($matches, [
                    'pattern' => 'date',
                    'token' => $token,
                    'i' => $i,
                    'j' => $j,
                    'separator' => '',
                    'year' => $bestCandidate['year'],
                    'month' => $bestCandidate['month'],
                    'day' => $bestCandidate['day'],
                ]);
            }
        }

        // dates with separators are between length 6 '1/1/91' and 10 '11/11/1991'
        for ($i = 0; $i < $passwordLength - 5; $i++) {
            for ($j = $i + 5; $j < $i + 10; $j++) {
                if ($j >= $passwordLength) {
                    break;
                }

                $token = substr($this->password, $i, $j - $i + 1);
                preg_match($maybeDateWithSeparator, $token, $rxMatch);
                if (!$rxMatch) {
                    continue;
                }

                $dmy = $this->mapIntsToDMY([
                    (int)$rxMatch[1],
                    (int)$rxMatch[3],
                    (int)$rxMatch[4],
                ]);
                if (!$dmy) {
                    continue;
                }

                array_push($matches, [
                    'pattern' => 'date',
                    'token' => $token,
                    'i' => $i,
                    'j' => $j,
                    'separator' => $rxMatch[2],
                    'year' => $dmy['year'],
                    'month' => $dmy['month'],
                    'day' => $dmy['day'],
                ]);
            }
        }

        // matches now contains all valid date strings in a way that is tricky to
        // capture with regexes only. while thorough, it will contain some
        // unintuitive noise:

        // '2015_06_04', in addition to matching 2015_06_04, will also contain
        // 5(!) other date matches: 15_06_04, 5_06_04, ..., even 2015
        // (matched as 5/1/2020)

        // to reduce noise, remove date matches that are strict substrings of others
        usort($matches, [$this, 'sortByIAndJ']);

        return array_values(array_filter($matches, function ($match) use ($matches) {
            $isSubmatch = false;
            foreach ($matches as $other) {
                if ($match === $other) {
                    continue;
                }
                if ($other['i'] <= $match['i'] and $other['j'] >= $match['j']) {
                    $isSubmatch = true;
                    break;
                }
            }
            return !$isSubmatch;
        }));
    }

    /**
     * given a 3-tuple, discard if:
     * middle int is over 31 (for all dmy formats, years are never allowed in
     * the middle)
     * middle int is zero
     * any int is over the max allowable year
     * any int is over two digits but under the min allowable year
     * 2 ints are over 31, the max allowable day
     * 2 ints are zero
     * all ints are over 12, the max allowable month
     *
     * @param $ints
     * @return null|array
     */
    protected function mapIntsToDMY($ints)
    {
        if ($ints[1] > 31 or $ints[1] <= 0) {
            return null;
        }

        $over12 = 0;
        $over31 = 0;
        $under1 = 0;

        foreach ($ints as $int) {
            if ((99 < $int and $int < self::DATE_MIN_YEAR) or $int > self::DATE_MAX_YEAR) {
                return null;
            }
            if ($int > 31) {
                $over31++;
            }
            if ($int > 12) {
                $over12++;
            }
            if ($int <= 0) {
                $under1++;
            }
        }

        if ($over31 >= 2 or $over12 == 3 or $under1 >= 2) {
            return null;
        }

        $possibleFourDigitSplits = [
            [$ints[2], array_slice($ints, 0, 2)],
            [$ints[0], array_slice($ints, 1, 2)],
        ];
        foreach ($possibleFourDigitSplits as list($y, $rest)) {
            if (self::DATE_MIN_YEAR <= $y and $y <= self::DATE_MAX_YEAR) {
                $dm = $this->mapIntsToDM($rest);
                if ($dm) {
                    return [
                        'year' => $y,
                        'month' => $dm['month'],
                        'day' => $dm['day'],
                    ];
                } else {
                    // for a candidate that includes a four-digit year,
                    // when the remaining ints don't match to a day and month,
                    // it is not a date.
                    return null;
                }
            }
        }

        // given no four-digit year, two digit years are the most flexible int to
        // match, so try to parse a day-month out of ints[0..1] or ints[1..0]
        foreach ($possibleFourDigitSplits as list($y, $rest)) {
            $dm = $this->mapIntsToDM($rest);
            if ($dm) {
                $y = $this->twoToFourDigitYear($y);
                return [
                    'year' => $y,
                    'month' => $dm['month'],
                    'day' => $dm['day'],
                ];
            }
        }

        return null;
    }

    protected function mapIntsToDM($ints)
    {
        foreach ([$ints, array_reverse($ints)] as list($d, $m)) {
            if (1 <= $d and $d <= 31 and 1 <= $m and $m <= 12) {
                return [
                    'day' => $d,
                    'month' => $m,
                ];
            }
        }
    }

    /**
     * @param $year
     * @return mixed
     */
    protected function twoToFourDigitYear($year)
    {
        if ($year > 99) {
            return $year;
        } else if ($year > 50) {
            // 87 -> 1987
            return $year + 1900;
        } else {
            // 15 -> 2015
            return $year + 2000;
        }
    }

    /**
     * @param $candidate
     * @return number
     */
    protected function metric($candidate)
    {
        return abs($candidate['year'] - Scoring::REFERENCE_YEAR);
    }
}