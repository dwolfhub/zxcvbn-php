<?php

namespace unit;

use PHPUnit_Framework_TestCase;
use Zxcvbn\TimeEstimates;

class TimeEstimatesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TimeEstimates
     */
    protected $timeEstimates;

    public function setUp()
    {
        $this->timeEstimates = new TimeEstimates();
    }

    public function testTimesPerHourAndSecond()
    {
        $times = $this->timeEstimates->estimateAttackTimes(2567800);

        $this->assertEquals(0.00025678, $times['crack_times_seconds']['offline_fast_hashing_1e10_per_second']);
        $this->assertEquals(256.78, $times['crack_times_seconds']['offline_slow_hashing_1e4_per_second']);
        $this->assertEquals(256780.0, $times['crack_times_seconds']['online_no_throttling_10_per_second']);
        $this->assertEquals(92440800.0, $times['crack_times_seconds']['online_throttling_100_per_hour']);
    }

    public function testDisplayTimes()
    {
        $times = $this->timeEstimates->estimateAttackTimes(2567800);

        $this->assertEquals(
            'less than a second',
            $times['crack_times_display']['offline_fast_hashing_1e10_per_second']
        );
        $this->assertEquals('4 minutes', $times['crack_times_display']['offline_slow_hashing_1e4_per_second']);
        $this->assertEquals('3 days', $times['crack_times_display']['online_no_throttling_10_per_second']);
        $this->assertEquals('3 years', $times['crack_times_display']['online_throttling_100_per_hour']);

        $times = $this->timeEstimates->estimateAttackTimes(1e10);

        $this->assertEquals('1 second', $times['crack_times_display']['offline_fast_hashing_1e10_per_second']);
        $this->assertEquals('12 days', $times['crack_times_display']['offline_slow_hashing_1e4_per_second']);
        $this->assertEquals('31 years', $times['crack_times_display']['online_no_throttling_10_per_second']);
        $this->assertEquals('centuries', $times['crack_times_display']['online_throttling_100_per_hour']);

        $times = $this->timeEstimates->estimateAttackTimes(1e17);

        $this->assertEquals('4 months', $times['crack_times_display']['offline_fast_hashing_1e10_per_second']);

        $times = $this->timeEstimates->estimateAttackTimes(1e14);

        $this->assertEquals('3 hours', $times['crack_times_display']['offline_fast_hashing_1e10_per_second']);
    }

    public function testGuessesToScore()
    {
        $this->assertEquals(0, $this->timeEstimates->estimateAttackTimes(1e3 + 4)['score']);
        $this->assertEquals(1, $this->timeEstimates->estimateAttackTimes(1e6 + 4)['score']);
        $this->assertEquals(2, $this->timeEstimates->estimateAttackTimes(1e8 + 4)['score']);
        $this->assertEquals(3, $this->timeEstimates->estimateAttackTimes(1e10 + 4)['score']);
        $this->assertEquals(4, $this->timeEstimates->estimateAttackTimes(1e10 + 5)['score']);
    }
}