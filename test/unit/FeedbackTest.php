<?php

namespace unit;

use PHPUnit_Framework_TestCase;
use Zxcvbn\Feedback;

class FeedbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Feedback
     */
    protected $feedback;

    public function setUp()
    {
        $this->feedback = new Feedback();
    }

    public function testReturnsDefaultFeedbackIfEmptySequence()
    {
        $this->assertEquals($this->feedback->getFeedback(0, []), Feedback::DEFAULT_FEEDBACK);
    }

    public function testReturnsEmptyFieldsWhenScoreGreaterThan2()
    {
        $feedback = $this->feedback->getFeedback(3, [1]);

        $this->assertArrayHasKey('warning', $feedback);
        $this->assertEmpty($feedback['warning']);

        $this->assertArrayHasKey('suggestions', $feedback);
        $this->assertEmpty($feedback['suggestions']);
    }

    public function testDictionaryMatchFeedback()
    {
        $feedback = $this->feedback->getFeedback(2, [
            [
                'dictionary_name' => 'passwords',
                'pattern' => 'dictionary',
                'token' => 'password',
                'reversed' => false,
                'l33t' => false,
                'rank' => 1,
            ]
        ]);
        $this->assertEquals('This is a top-10 common password', $feedback['warning']);

        $feedback = $this->feedback->getFeedback(2, [
            [
                'dictionary_name' => 'passwords',
                'pattern' => 'dictionary',
                'token' => 'password',
                'reversed' => false,
                'l33t' => false,
                'rank' => 100
            ]
        ]);
        $this->assertEquals('This is a top-100 common password', $feedback['warning']);

        $feedback = $this->feedback->getFeedback(2, [
            [
                'dictionary_name' => 'passwords',
                'pattern' => 'dictionary',
                'token' => 'password',
                'reversed' => false,
                'l33t' => false,
                'rank' => 101
            ]
        ]);
        $this->assertEquals('This is a very common password', $feedback['warning']);

        $feedback = $this->feedback->getFeedback(2, [
            [
                'dictionary_name' => 'passwords',
                'reversed' => true,
                'l33t' => false,
                'rank' => 101,
                'guesses_log10' => 4,
                'pattern' => 'dictionary',
                'token' => 'password',
            ]
        ]);
        $this->assertEquals('This is similar to a commonly used password', $feedback['warning']);

        $feedback = $this->feedback->getFeedback(2, [
            [
                'dictionary_name' => 'english',
                'reversed' => false,
                'l33t' => false,
                'rank' => 101,
                'guesses_log10' => 4,
                'pattern' => 'dictionary',
                'token' => 'although',
            ]
        ]);
        $this->assertEquals('A word by itself is easy to guess', $feedback['warning']);

        $feedback = $this->feedback->getFeedback(2, [
            [
                'dictionary_name' => 'surnames',
                'reversed' => false,
                'l33t' => false,
                'rank' => 101,
                'guesses_log10' => 4,
                'pattern' => 'dictionary',
                'token' => 'rosemary',
            ], [
                'dictionary_name' => 'english',
                'reversed' => false,
                'l33t' => false,
                'rank' => 101,
                'guesses_log10' => 4,
                'pattern' => 'dictionary',
                'token' => 'although',
            ]
        ]);
        $this->assertEquals('Common names and surnames are easy to guess', $feedback['warning']);

        $feedback = $this->feedback->getFeedback(2, [
            [
                'dictionary_name' => 'surnames',
                'reversed' => false,
                'l33t' => false,
                'rank' => 101,
                'guesses_log10' => 4,
                'pattern' => 'dictionary',
                'token' => 'Rosemary',
            ]
        ]);
        $this->assertContains("Capitalization doesn't help very much", $feedback['suggestions']);

        $feedback = $this->feedback->getFeedback(2, [
            [
                'dictionary_name' => 'surnames',
                'reversed' => false,
                'l33t' => false,
                'rank' => 101,
                'guesses_log10' => 4,
                'pattern' => 'dictionary',
                'token' => 'ROSEMARY',
            ]
        ]);
        $this->assertContains('All-uppercase is almost as easy to guess as all-lowercase', $feedback['suggestions']);

        $feedback = $this->feedback->getFeedback(2, [
            [
                'dictionary_name' => 'surnames',
                'reversed' => true,
                'l33t' => false,
                'rank' => 101,
                'guesses_log10' => 4,
                'pattern' => 'dictionary',
                'token' => 'ROSEMARY',
            ]
        ]);
        $this->assertContains("Reversed words aren't much harder to guess", $feedback['suggestions']);

        $feedback = $this->feedback->getFeedback(2, [
            [
                'dictionary_name' => 'surnames',
                'reversed' => true,
                'l33t' => true,
                'rank' => 101,
                'guesses_log10' => 4,
                'pattern' => 'dictionary',
                'token' => 'ROSEMARY',
            ]
        ]);
        $this->assertContains("Predictable substitutions like '@' instead of 'a' don't help very much", $feedback['suggestions']);

    }

    public function testSpatialMatchFeedback()
    {
        $feedback = $this->feedback->getFeedback(2, [
            [
                'pattern' => 'spatial',
                'turns' => 1,
            ]
        ]);
        $this->assertEquals('Straight rows of keys are easy to guess', $feedback['warning']);

        $feedback = $this->feedback->getFeedback(2, [
            [
                'pattern' => 'spatial',
                'turns' => 2,
            ]
        ]);
        $this->assertEquals('Short keyboard patterns are easy to guess', $feedback['warning']);
        $this->assertContains('Use a longer keyboard pattern with more turns', $feedback['suggestions']);
    }

    public function testRepeatMatchFeedback()
    {
        $feedback = $this->feedback->getFeedback(2, [
            [
                'pattern' => 'repeat',
                'base_token' => 'a',
            ]
        ]);
        $this->assertEquals('Repeats like "aaa" are easy to guess', $feedback['warning']);

        $feedback = $this->feedback->getFeedback(2, [
            [
                'pattern' => 'repeat',
                'base_token' => 'abc',
            ]
        ]);
        $this->assertEquals('Repeats like "abcabcabc" are only slightly harder to guess than "abc"', $feedback['warning']);
        $this->assertContains('Avoid repeated words and characters', $feedback['suggestions']);
    }

    public function testSequenceMatchFeedback()
    {
        $feedback = $this->feedback->getFeedback(2, [
            [
                'pattern' => 'sequence',
                'token' => '123456'
            ]
        ]);

        $this->assertEquals('Sequences like abc or 6543 are easy to guess', $feedback['warning']);
        $this->assertContains('Avoid sequences', $feedback['suggestions']);
    }

    public function testRegexMatchFeedback()
    {
        $feedback = $this->feedback->getFeedback(2, [
            [
                'pattern' => 'regex',
                'regex_name' => 'recent_year',
            ]
        ]);

        $this->assertEquals('Recent years are easy to guess', $feedback['warning']);
        $this->assertContains('Avoid recent years', $feedback['suggestions']);
        $this->assertContains( 'Avoid years that are associated with you', $feedback['suggestions']);
    }

    public function testDateMatchFeedback()
    {
        $feedback = $this->feedback->getFeedback(2, [
            [
                'pattern' => 'date',
            ]
        ]);

        $this->assertEquals('Dates are often easy to guess', $feedback['warning']);
        $this->assertContains('Avoid dates and years that are associated with you', $feedback['suggestions']);
    }

    public function testBaseSuggestionAlwaysAdded()
    {
        $feedback = $this->feedback->getFeedback(2, [
            [
                'pattern' => 'date',
            ]
        ]);

        $this->assertContains('Add another word or two. Uncommon words are better.', $feedback['suggestions']);
    }

}