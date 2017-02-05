<?php

namespace test;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Feedback;
use Zxcvbn\Match\AbstractMatch;
use Zxcvbn\Scoring;
use Zxcvbn\TimeEstimates;
use Zxcvbn\Zxcvbn;

class ZxcvbnTest extends TestCase
{
    public function testCalculateStrength()
    {
        $mockMatch = $this->getMockBuilder(AbstractMatch::class)
            ->setMethods(['getMatches', 'addRankedDictionary'])
            ->getMock();
        $mockMatch->expects($this->once())
            ->method('getMatches')
            ->willReturn(['mockmatch']);
        $mockMatch->expects($this->once())
            ->method('addRankedDictionary')
            ->with('user_inputs', ['pass', 'word']); // @todo

        $mockScoring = $this->getMockBuilder(Scoring::class)
            ->disableOriginalConstructor()
            ->setMethods(['mostGuessableMatchSequence'])
            ->getMock();
        $mockScoring->expects($this->once())
            ->method('mostGuessableMatchSequence')
            ->with('password', ['mockmatch'])
            ->willReturn([
                'guesses' => 1,
                'sequence' => ['mocksequence'],
            ]);

        $mockTimeEstimates = $this->getMockBuilder(TimeEstimates::class)
            ->setMethods(['estimateAttackTimes'])
            ->getMock();
        $mockTimeEstimates->expects($this->once())
            ->method('estimateAttackTimes')
            ->willReturn([
                'score' => 1,
            ]);

        $mockFeedback = $this->getMockBuilder(Feedback::class)
            ->setMethods(['getFeedback'])
            ->getMock();
        $mockFeedback->expects($this->once())
            ->method('getFeedback')
            ->willReturn([
                'warning' => 'THIS IS A WARNING',
                'suggestions' => [
                    'SUGGESTION 1',
                    'SUGGESTION 2',
                ],
            ]);

        $zxcvbn = new Zxcvbn(
            $mockMatch,
            $mockScoring,
            $mockTimeEstimates,
            $mockFeedback
        );

        $strength = $zxcvbn->calculateStrength('password', ['pass', 'word']);

        $this->assertEquals(1, $strength['guesses']);
        $this->assertEquals(['mocksequence'], $strength['sequence']);
        $this->assertEquals(1, $strength['score']);
        $this->assertEquals('THIS IS A WARNING', $strength['feedback']['warning']);
        $this->assertContains('SUGGESTION 1', $strength['feedback']['suggestions']);
        $this->assertContains('SUGGESTION 2', $strength['feedback']['suggestions']);
        $this->assertInternalType('float', $strength['calc_time']);
        $this->assertLessThan(.1, $strength['calc_time']);
    }

    public function testPasswordStrength()
    {
        $result1 = Zxcvbn::passwordStrength('password');
        $this->assertEquals('password', $result1['password']);
        $this->assertEquals(20150, $result1['guesses']);
        $this->assertEquals(4.3042750504771279, $result1['guesses_log10']);
        $this->assertEquals([
            [
                'pattern' => 'dictionary',
                'i' => 0,
                'j' => 7,
                'token' => 'password',
                'matched_word' => 'password',
                'rank' => 10075,
                'dictionary_name' => 'passwords',
                'reversed' => false,
                'l33t' => false,
                'guesses' => 20150,
                'guesses_log10' => 4.3042750504771279,
            ],
        ], $result1['sequence']);
        $this->assertEquals(725400.0, $result1['crack_times_seconds']['online_throttling_100_per_hour'], '', .000001);
        $this->assertEquals(2015.0, $result1['crack_times_seconds']['online_no_throttling_10_per_second'], '', .000001);
        $this->assertEquals(2.015, $result1['crack_times_seconds']['offline_slow_hashing_1e4_per_second'], '', .000001);
        $this->assertEquals(
            2.015e-6,
            $result1['crack_times_seconds']['offline_fast_hashing_1e10_per_second'],
            '',
            .000001
        );
        $this->assertEquals([
            'online_throttling_100_per_hour' => '8 days',
            'online_no_throttling_10_per_second' => '34 minutes',
            'offline_slow_hashing_1e4_per_second' => '2 seconds',
            'offline_fast_hashing_1e10_per_second' => 'less than a second',
        ], $result1['crack_times_display']);
        $this->assertEquals(1, $result1['score']);
        $this->assertEquals([
            'warning' => '',
            'suggestions' => [
                'Add another word or two. Uncommon words are better.',
                'Reversed words aren\'t much harder to guess',
            ],
        ], $result1['feedback']);

        $result1 = Zxcvbn::passwordStrength('JohnSmith123', ['John', 'Smith']);
        $this->assertEquals('JohnSmith123', $result1['password']);
        $this->assertEquals(73834600, $result1['guesses']);
        $this->assertEquals(7.86825992642577, $result1['guesses_log10']);
        $this->assertEquals([
            [
                'pattern' => 'dictionary',
                'i' => 0,
                'j' => 8,
                'token' => 'JohnSmith',
                'matched_word' => 'johnsmith',
                'rank' => 16051,
                'dictionary_name' => 'passwords',
                'reversed' => false,
                'l33t' => false,
                'guesses' => 738346,
                'guesses_log10' => 5.86825992642577,
            ],
            [
                'pattern' => 'sequence',
                'i' => 9,
                'j' => 11,
                'token' => '123',
                'sequence_name' => 'digits',
                'sequence_space' => 10,
                'ascending' => true,
                'guesses' => 50,
                'guesses_log10' => 1.6989700043360187,
            ],
        ], $result1['sequence']);
        $this->assertEquals(2658045600.0, $result1['crack_times_seconds']['online_throttling_100_per_hour'], '', .000001);
        $this->assertEquals(7383460.0, $result1['crack_times_seconds']['online_no_throttling_10_per_second'], '', .000001);
        $this->assertEquals(7383.46, $result1['crack_times_seconds']['offline_slow_hashing_1e4_per_second'], '', .000001);
        $this->assertEquals(
            0.0073834599999999997,
            $result1['crack_times_seconds']['offline_fast_hashing_1e10_per_second'],
            '',
            .000001
        );
        $this->assertEquals([
            'online_throttling_100_per_hour' => '83 years',
            'online_no_throttling_10_per_second' => '3 months',
            'offline_slow_hashing_1e4_per_second' => '2 hours',
            'offline_fast_hashing_1e10_per_second' => 'less than a second',
        ], $result1['crack_times_display']);
        $this->assertEquals(2, $result1['score']);
        $this->assertEquals([
            'warning' => '',
            'suggestions' => [
                'Add another word or two. Uncommon words are better.',
            ],
        ], $result1['feedback']);


    }
}
