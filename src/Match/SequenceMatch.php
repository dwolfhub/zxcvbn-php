<?php

namespace Zxcvbn\Match;

/**
 * Identifies sequences by looking for repeated differences in unicode codepoint.
 * this allows skipping, such as 9753, and also matches some extended unicode sequences
 * such as Greek and Cyrillic alphabets.
 *
 * for example, consider the input 'abcdb975zy'
 *
 * password: a   b   c   d   b    9   7   5   z   y
 * index:    0   1   2   3   4    5   6   7   8   9
 * delta:      1   1   1  -2  -41  -2  -2  69   1
 *
 * expected result:
 * [(i, j, delta), ...] = [(0, 3, 1), (5, 7, -2), (8, 9, 1)]
 *
 * Class SequenceMatch
 * @package Zxcvbn\Match
 */
class SequenceMatch extends AbstractMatch
{
    /**
     * @var int
     */
    const MAX_DELTA = 5;

    /**
     * {@inheritdoc}
     */
    public function getMatches()
    {
        if (strlen($this->password) === 1) {
            return [];
        }

        $result = [];
        $i = 0;
        $lastDelta = null;

        for ($k = 1; $k < strlen($this->password); $k++) {
            $delta = ord(substr($this->password, $k, 1)) - ord(substr($this->password, $k - 1, 1));
            if ($lastDelta === null) {
                $lastDelta = $delta;
            }
            if ($delta === $lastDelta) {
                continue;
            }
            $j = $k - 1;
            $result = $this->update($result, $i, $j, $lastDelta);
            $i = $j;
            $lastDelta = $delta;
        }
        $result = $this->update($result, $i, strlen($this->password) - 1, $lastDelta);

        return $result;
    }

    /**
     * @param $result
     * @param $i
     * @param $j
     * @param $delta
     * @return array
     */
    protected function update($result, $i, $j, $delta)
    {
        if ($j - 1 > 1 or ($delta and abs($delta) === 1)) {
            if (0 < abs($delta) and abs($delta) <= self::MAX_DELTA) {
                $token = substr($this->password, $i, $j - $i + 1);
                if (preg_match('/^[a-z]+$/', $token)) {
                    $sequenceName = 'lower';
                    $sequenceSpace = 26;
                } else if (preg_match('/^[A-Z]+$/', $token)) {
                    $sequenceName = 'upper';
                    $sequenceSpace = 26;
                } else if (preg_match('/^\d+$/', $token)) {
                    $sequenceName = 'digits';
                    $sequenceSpace = 10;
                } else {
                    $sequenceName = 'unicode';
                    $sequenceSpace = 26;
                }

                $result[] = [
                    'pattern' => 'sequence',
                    'i' => $i,
                    'j' => $j,
                    'token' => substr($this->password, $i, $j - $i + 1),
                    'sequence_name' => $sequenceName,
                    'sequence_space' => $sequenceSpace,
                    'ascending' => $delta > 0
                ];
            }
        }

        return $result;
    }
}