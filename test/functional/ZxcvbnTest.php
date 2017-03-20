<?php
namespace ZxcvbnPhp\test\functional;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Zxcvbn;

class ZxcvbnTest extends TestCase
{
    public function testPasswordStrength()
    {
        $result1 = Zxcvbn::passwordStrength('password');
        $this->assertEquals('password', $result1['password']);
        $this->assertEquals(3, $result1['guesses']);
        $this->assertEquals(0.47712125471966244, $result1['guesses_log10']);
        $this->assertEquals([
            [
                'l33t_variations' => 1,
                'uppercase_variations' => 1,
                'pattern' => 'dictionary',
                'j' => 7,
                'i' => 0,
                'token' => 'password',
                'matched_word' => 'password',
                'rank' => 2,
                'dictionary_name' => 'passwords',
                'reversed' => false,
                'l33t' => false,
                'guesses' => 2,
                'base_guesses' => 2,
                'guesses_log10' => 0.30102999566398114,
            ],
        ], $result1['sequence']);
        $this->assertEquals(108.0, $result1['crack_times_seconds']['online_throttling_100_per_hour'], '', .000001);
        $this->assertEquals(2015.0, $result1['crack_times_seconds']['online_no_throttling_10_per_second'], '', 0.3);
        $this->assertEquals(2.015, $result1['crack_times_seconds']['offline_slow_hashing_1e4_per_second'], '', .0003);
        $this->assertEquals(
            3e-10,
            $result1['crack_times_seconds']['offline_fast_hashing_1e10_per_second'],
            '',
            .000001
        );
        $this->assertEquals([
            'online_throttling_100_per_hour' => '2 minutes',
            'online_no_throttling_10_per_second' => 'less than a second',
            'offline_slow_hashing_1e4_per_second' => 'less than a second',
            'offline_fast_hashing_1e10_per_second' => 'less than a second',
        ], $result1['crack_times_display']);
        $this->assertEquals(0, $result1['score']);
        $this->assertEquals([
            'warning' => '',
            'suggestions' => [
                'This is a top-10 common password.',
                'Add another word or two. Uncommon words are better.',
            ],
        ], $result1['feedback']);

        $result1 = Zxcvbn::passwordStrength('JohnSmith123', ['John', 'Smith']);
        $this->assertEquals('JohnSmith123', $result1['password']);
        $this->assertEquals(2567800, $result1['guesses']);
        $this->assertEquals(6.409561194521849, $result1['guesses_log10']);
        $this->assertEquals([
            [
                'l33t_variations' => 1,
                'pattern' => 'dictionary',
                'j' => 3,
                'dictionary_name' => 'user_inputs',
                'token' => 'John',
                'i' => 0,
                'l33t' => false,
                'rank' => 1,
                'guesses_log10' => 1.6989700043360185,
                'uppercase_variations' => 2,
                'base_guesses' => 1,
                'reversed' => false,
                'matched_word' => 'john',
                'guesses' => 50,
            ],
            [
                'l33t_variations' => 1,
                'pattern' => 'dictionary',
                'j' => 11,
                'dictionary_name' => 'passwords',
                'token' => 'Smith123',
                'i' => 4,
                'l33t' => false,
                'rank' => 12789,
                'guesses_log10' => 4.407866583030775,
                'uppercase_variations' => 2,
                'base_guesses' => 12789,
                'reversed' => false,
                'matched_word' => 'smith123',
                'guesses' => 25578,
            ],
        ], $result1['sequence']);
        $this->assertEquals(
            92440800.0,
            $result1['crack_times_seconds']['online_throttling_100_per_hour'], '',
            .000001
        );
        $this->assertEquals(
            256780.0,
            $result1['crack_times_seconds']['online_no_throttling_10_per_second'], '',
            .000001
        );
        $this->assertEquals(
            256.78,
            $result1['crack_times_seconds']['offline_slow_hashing_1e4_per_second'], '',
            .000001
        );
        $this->assertEquals(
            0.00025678,
            $result1['crack_times_seconds']['offline_fast_hashing_1e10_per_second'],
            '',
            .000001
        );
        $this->assertEquals([
            'online_throttling_100_per_hour' => '3 years',
            'online_no_throttling_10_per_second' => '3 days',
            'offline_slow_hashing_1e4_per_second' => '4 minutes',
            'offline_fast_hashing_1e10_per_second' => 'less than a second',
        ], $result1['crack_times_display']);
        $this->assertEquals(2, $result1['score']);
        $this->assertEquals([
            'warning' => '',
            'suggestions' => [
                'Add another word or two. Uncommon words are better.',
                'Capitalization doesn\'t help very much.'
            ],
        ], $result1['feedback']);
    }
}