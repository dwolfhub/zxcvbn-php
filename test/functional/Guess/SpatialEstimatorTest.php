<?php

namespace test\functional\Guess;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Zxcvbn\Guess\SpatialEstimator;
use Zxcvbn\Match\DataProvider\AdjacencyGraphs;

class SpatialEstimatorTest extends TestCase
{
    public function testSpatialEstimates()
    {
        $spatialEstimator = new SpatialEstimator();

        $match = [
            'token' => 'zxcvbn',
            'graph' => 'qwerty',
            'turns' => 1,
            'shifted_count' => 0,
        ];
        $qwertyGraphs = AdjacencyGraphs::getData()['qwerty'];
        $keyboardStartingPositions = count(array_keys($qwertyGraphs));

        $keyboardAverageDegree = 0;
        foreach ($qwertyGraphs as $key => $neighbors) {
            foreach ($neighbors as $neighbor) {
                if ($neighbor) {
                    $keyboardAverageDegree += 1;
                }
            }
        }
        $keyboardAverageDegree /= count($qwertyGraphs);

        $baseGuesses = $keyboardStartingPositions * $keyboardAverageDegree * (strlen($match['token']) - 1);
        $msg = 'with no turns or shifts, guesses is starts * degree * (len-1)';
        $this->assertEquals($baseGuesses, $spatialEstimator->estimate($match), $msg);

        $reflectedSpatialEstimator = new ReflectionClass(SpatialEstimator::class);
        $nCKMethod = $reflectedSpatialEstimator->getMethod('nCK');
        $nCKMethod->setAccessible(true);

        $match['guesses'] = null;
        $match['token'] = 'ZxCvbn';
        $match['shifted_count'] = 2;
        $shiftedGuesses = $baseGuesses * (
                $nCKMethod->invokeArgs($spatialEstimator, [6, 2])
                + $nCKMethod->invokeArgs($spatialEstimator, [6, 1])
            );
        $msg = 'guesses is added for shifted keys, similar to capitals in dictionary matching';
        $this->assertEquals($shiftedGuesses, $spatialEstimator->estimate($match), $msg);

        $match['guesses'] = null;
        $match['token'] = 'ZXCVBN';
        $match['shifted_count'] = 6;
        $shiftedGuesses = $baseGuesses * 2;
        $msg = 'when everything is shifted, guesses are doubled';
        $this->assertEquals($shiftedGuesses, $spatialEstimator->estimate($match), $msg);

        $match = [
            'token' => 'zxcft6yh',
            'graph' => 'qwerty',
            'turns' => 3,
            'shifted_count' => 0,
        ];
        $guesses = 0;
        $L = strlen($match['token']);
        $s = $keyboardStartingPositions;
        $d = $keyboardAverageDegree;
        for ($i = 2; $i < $L + 1; $i++) {
            for ($j = 1; $j < min($match['turns'], $i - 1) + 1; $j++) {
                $guesses += $nCKMethod->invokeArgs($spatialEstimator, [$i - 1, $j - 1]) * $s * $d ** $j;
            }
        }

        $msg = 'spatial guesses accounts for turn positions, directions and starting keys';
        $this->assertEquals($guesses, $spatialEstimator->estimate($match));
    }

}