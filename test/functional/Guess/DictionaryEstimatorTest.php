<?php

namespace test\functional\Guess;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Guess\DictionaryEstimator;

class DictionaryEstimatorTest extends TestCase
{
    public function testDictionaryGuesses()
    {
        $dictionaryEstimator = new DictionaryEstimator();
        $reflectionDictionaryEstimator = new \ReflectionClass(DictionaryEstimator::class);
        $uppercaseVariations = $reflectionDictionaryEstimator->getMethod('uppercaseVariations');
        $uppercaseVariations->setAccessible(true);
        $l33tVariations = $reflectionDictionaryEstimator->getMethod('l33tVariations');
        $l33tVariations->setAccessible(true);

        $match = [
            'token' => 'aaaaa',
            'rank' => 32,
        ];
        $msg = 'base guesses == the rank';
        $this->assertEquals(32, $dictionaryEstimator->estimate($match), $msg);


        $match = [
            'token' => 'AAAaaa',
            'rank' => 32,
        ];
        $msg = 'extra guesses are added for capitalization';
        $this->assertEquals(
            32 * $uppercaseVariations->invokeArgs($dictionaryEstimator, [$match]),
            $dictionaryEstimator->estimate($match),
            $msg
        );

        $match = [
            'token' => 'aaa',
            'rank' => 32,
            'reversed' => true,
        ];
        $msg = 'guesses are doubled when word is reversed';
        $this->assertEquals(32 * 2, $dictionaryEstimator->estimate($match), $msg);

        $match = [
            'token' => 'aaa@@@',
            'rank' => 32,
            'l33t' => true,
            'sub' => ['@' => 'a'],
        ];
        $msg = 'extra guesses are added for both capitalization and common l33t substitutions';
        $this->assertEquals(
            32 * $l33tVariations->invokeArgs($dictionaryEstimator, [$match])
            * $uppercaseVariations->invokeArgs($dictionaryEstimator, [$match]),
            $dictionaryEstimator->estimate($match),
            $msg
        );
    }
}