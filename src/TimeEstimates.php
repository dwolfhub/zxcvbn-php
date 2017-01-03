<?php

namespace Zxcvbn;

/**
 * Class TimeEstimates
 * @package Zxcvbn
 */
class TimeEstimates
{
    /**
     * @var int
     */
    protected $guesses;

    /**
     * TimeEstimates constructor.
     * @param $guesses
     */
    public function __construct($guesses)
    {
        $this->guesses = $guesses;
    }

    /**
     * @return array
     */
    public function estimateAttackTimes()
    {
        $crackTimesSeconds = [
            'online_throttling_100_per_hour' => $this->guesses / (100 / 3600),
            'online_no_throttling_10_per_second' => $this->guesses / 10,
            'offline_slow_hashing_1e4_per_second' => $this->guesses / 1e4,
            'offline_fast_hashing_1e10_per_second' => $this->guesses / 1e10,
        ];

        $crackTimesDisplay = [];
        foreach ($crackTimesSeconds as $scenario => $seconds) {
            $crackTimesDisplay[$scenario] = $this->displayTime($seconds);
        }

        return [
            'crack_times_seconds' => $crackTimesSeconds,
            'crack_times_display' => $crackTimesDisplay,
            'score' => $this->guessesToScore(),
        ];
    }

    /**
     * @return int
     */
    protected function guessesToScore()
    {
        $delta = 5;

        if ($this->guesses < 1e3 + $delta) {
            # risky password: "too guessable"
            return 0;
        } else if ($this->guesses < 1e6 + $delta) {
            # modest protection from throttled online attacks: "very guessable"
            return 1;
        } else if ($this->guesses < 1e8 + $delta) {
            # modest protection from unthrottled online attacks: "somewhat
            # guessable"
            return 2;
        } else if ($this->guesses < 1e10 + $delta) {
            # modest protection from offline attacks: "safely unguessable"
            # assuming a salted, slow hash function like bcrypt, scrypt, PBKDF2,
            # argon, etc
            return 3;
        } else {
            # strong protection from offline attacks under same scenario: "very
            # unguessable"
            return 4;
        }
    }

    /**
     * @param $seconds
     * @return string
     */
    protected function displayTime($seconds)
    {
        $minute = 60;
        $hour = $minute * 60;
        $day = $hour * 24;
        $month = $day * 31;
        $year = $month * 12;
        $century = $year * 100;

        if ($seconds < 1) {
            $display_num = null;
            $display_str = 'less than a second';
        } else if ($seconds < $minute) {
            $base = round($seconds);
            $display_num = $base;
            $display_str = sprintf('%s second', $base);
        } else if ($seconds < $hour) {
            $base = round($seconds / $minute);
            $display_num = $base;
            $display_str = sprintf('%s minute', $base);
        } else if ($seconds < $day) {
            $base = round($seconds / $hour);
            $display_num = $base;
            $display_str = sprintf('%s hour', $base);
        } else if ($seconds < $month) {
            $base = round($seconds / $day);
            $display_num = $base;
            $display_str = sprintf('%s day', $base);
        } else if ($seconds < $year) {
            $base = round($seconds / $month);
            $display_num = $base;
            $display_str = sprintf('%s month', $base);
        } else if ($seconds < $century) {
            $base = round($seconds / $year);
            $display_num = $base;
            $display_str = sprintf('%s year', $base);
        } else {
            $display_num = null;
            $display_str = 'centuries';
        }

        if ($display_num and $display_num != 1) {
            $display_str .= 's';
        }

        return $display_str;
    }
}