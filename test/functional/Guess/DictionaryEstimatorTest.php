<?php

namespace test\functional\Guess;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
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

    /**
     * @dataProvider uppercaseVariantsDataProvider
     */
    public function testUppercaseVariants($word, $variants)
    {
        $dictionaryEstimator = new DictionaryEstimator();
        $reflectionDictionaryEstimator = new \ReflectionClass(DictionaryEstimator::class);
        $uppercaseVariations = $reflectionDictionaryEstimator->getMethod('uppercaseVariations');
        $uppercaseVariations->setAccessible(true);

        $msg = sprintf('guess multiplier of %s is %s', $word, $variants);
        $this->assertEquals(
            $variants,
            $uppercaseVariations->invokeArgs($dictionaryEstimator, [['token' => $word]]),
            $msg
        );
    }

    public function uppercaseVariantsDataProvider()
    {
        $dictionaryEstimator = new DictionaryEstimator();
        $nCKMethod = $this->getNCKMethod();

        return [
            ['', 1],
            ['a', 1],
            ['A', 2],
            ['abcdef', 1],
            ['Abcdef', 2],
            ['abcdeF', 2],
            ['ABCDEF', 2],
            ['aBcdef', $nCKMethod->invokeArgs($dictionaryEstimator, [6, 1])],
            [
                'aBcDef',
                $nCKMethod->invokeArgs($dictionaryEstimator, [6, 1]) + $nCKMethod->invokeArgs($dictionaryEstimator,
                    [6, 2])
            ],
            ['ABCDEf', $nCKMethod->invokeArgs($dictionaryEstimator, [6, 1])],
            [
                'aBCDEf',
                $nCKMethod->invokeArgs($dictionaryEstimator, [6, 1]) + $nCKMethod->invokeArgs($dictionaryEstimator,
                    [6, 2])
            ],
            [
                'ABCdef',
                $nCKMethod->invokeArgs($dictionaryEstimator, [6, 1]) + $nCKMethod->invokeArgs($dictionaryEstimator,
                    [6, 2]) + $nCKMethod->invokeArgs($dictionaryEstimator, [6, 3])
            ],
        ];
    }

    /**
     * @dataProvider l33tVariantsDataProvider
     */
    public function testL33tVariants($word, $variants, $sub)
    {
        $dictionaryEstimator = new DictionaryEstimator();
        $reflectionDictionaryEstimator = new \ReflectionClass(DictionaryEstimator::class);
        $l33tVariations = $reflectionDictionaryEstimator->getMethod('l33tVariations');
        $l33tVariations->setAccessible(true);

        $match = [
            'token' => $word,
            'sub' => $sub,
            'l33t' => count($sub) > 0,
        ];
        $msg = sprintf('extra l33t guesses of %s is %s', $word, $variants);
        $this->assertEquals($variants, $l33tVariations->invokeArgs($dictionaryEstimator, [$match]), $msg);
    }

    public function l33tVariantsDataProvider()
    {
        $dictionaryEstimator = new DictionaryEstimator();
        $nCKMethod = $this->getNCKMethod();

        return [
            ['', 1, []],
            ['a', 1, []],
            ['4', 2, ['4' => 'a']],
            ['4pple', 2, ['4' => 'a']],
            ['abcet', 1, []],
            ['4bcet', 2, ['4' => 'a']],
            ['a8cet', 2, ['8' => 'b']],
            ['abce+', 2, ['+' => 't']],
            ['48cet', 4, ['4' => 'a', '8' => 'b']],
            [
                'a4a4aa',
                $nCKMethod->invokeArgs(
                    $dictionaryEstimator, [6, 2]) + $nCKMethod->invokeArgs($dictionaryEstimator,
                    [6, 1]
                ),
                ['4' => 'a']
            ],
            [
                '4a4a44',
                $nCKMethod->invokeArgs(
                    $dictionaryEstimator, [6, 2]) + $nCKMethod->invokeArgs($dictionaryEstimator,
                    [6, 1]
                ),
                ['4' => 'a']
            ],
            [
                'a44att+',
                ($nCKMethod->invokeArgs($dictionaryEstimator, [4, 2]) + $nCKMethod->invokeArgs($dictionaryEstimator,
                        [4, 1])) * $nCKMethod->invokeArgs($dictionaryEstimator, [3, 1]),
                ['4' => 'a', '+' => 't']
            ],
        ];
    }

    public function testCapitalizationDoesntEffectL33tGuesses()
    {
        $dictionaryEstimator = new DictionaryEstimator();
        $reflectionDictionaryEstimator = new \ReflectionClass(DictionaryEstimator::class);
        $l33tVariations = $reflectionDictionaryEstimator->getMethod('l33tVariations');
        $l33tVariations->setAccessible(true);

        $nCKMethod = $this->getNCKMethod();
        $match = [
            'token' => 'Aa44aA',
            'l33t' => true,
            'sub' => ['4' => 'a'],
        ];
        $variants = $nCKMethod->invokeArgs($dictionaryEstimator, [6, 2]) + $nCKMethod->invokeArgs($dictionaryEstimator, [6, 1]);
        $msg = 'capitalization does not affect extra l33t guesses calc';
        $this->assertEquals($variants, $l33tVariations->invokeArgs($dictionaryEstimator, [$match]), $msg);
    }

    /**
     * @return ReflectionMethod
     */
    private function getNCKMethod()
    {
        $reflectedSpatialEstimator = new ReflectionClass(DictionaryEstimator::class);
        $nCKMethod = $reflectedSpatialEstimator->getMethod('nCK');
        $nCKMethod->setAccessible(true);

        return $nCKMethod;
    }
}