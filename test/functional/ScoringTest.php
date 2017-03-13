<?php
namespace ZxcvbnPhp\test\functional;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Zxcvbn\Guess\AbstractEstimator;
use Zxcvbn\Guess\DateEstimator;
use Zxcvbn\Guess\EstimatorFactory;
use Zxcvbn\Scoring;

class ScoringTest extends TestCase
{
    public function testReturnsOneBruteForceMatchGivenAnEmptyMatchSequence()
    {
        $scoring = new Scoring(new EstimatorFactory());
        $password = '0123456789';

        $result = $scoring->mostGuessableMatchSequence($password, []);

        $this->assertCount(1, $result['sequence']);

        $sequence = $result['sequence'][0];

        $this->assertEquals('bruteforce', $sequence['pattern']);
        $this->assertEquals($password, $sequence['token']);
        $this->assertEquals(0, $sequence['i']);
        $this->assertEquals(9, $sequence['j']);
    }

    public function testReturnsMatchPlusBruteForceWhenMatchCoversAPrefixOfPassword()
    {
        $scoring = new Scoring(new EstimatorFactory());
        $password = '0123456789';

        $match = $this->getMockMatchArray(0, 5, 1);
        $matches = [$match];

        $result = $scoring->mostGuessableMatchSequence($password, $matches, true);

        $this->assertCount(2, $result['sequence']);
        $this->assertEquals($match, $result['sequence'][0]);

        $bruteForceMatch = $result['sequence'][1];

        $this->assertEquals('bruteforce', $bruteForceMatch['pattern']);
        $this->assertEquals(6, $bruteForceMatch['i']);
        $this->assertEquals(9, $bruteForceMatch['j']);
    }

    public function testReturnsBruteForcePlusMatchWhenMatchCoversASuffix()
    {
        $scoring = new Scoring(new EstimatorFactory());
        $password = '0123456789';

        $match = $this->getMockMatchArray(3, 9, 1);
        $matches = [$match];
        $result = $scoring->mostGuessableMatchSequence($password, $matches, true);

        $this->assertCount(2, $result['sequence']);
        $bruteForceMatch = $result['sequence'][0];
        $this->assertEquals('bruteforce', $bruteForceMatch['pattern']);
        $this->assertEquals(0, $bruteForceMatch['i']);
        $this->assertEquals(2, $bruteForceMatch['j']);

        $this->assertEquals($match, $result['sequence'][1]);
    }

    public function testChoosesLowerGuessesMatchGivenTwoMatchesOfTheSameSpan()
    {
        $scoring = new Scoring(new EstimatorFactory());
        $password = '0123456789';

        $match0 = $this->getMockMatchArray(0, 9, 1);
        $match1 = $this->getMockMatchArray(0, 9, 2);
        $matches = [$match0, $match1];

        $result = $scoring->mostGuessableMatchSequence($password, $matches, true);

        $this->assertCount(1, $result['sequence']);
        $this->assertEquals($match0, $result['sequence'][0]);

        $match0['guesses'] = 3;
        $matches[0] = $match0;
        $this->assertEquals(3, $matches[0]['guesses']);

        $result = $scoring->mostGuessableMatchSequence($password, $matches, true);
        $this->assertCount(1, $result['sequence']);
        $this->assertEquals($match1, $result['sequence'][0]);
    }

    public function testWhenMatch0CoversMatch1And2ChooseMatch()
    {
        $scoring = new Scoring(new EstimatorFactory());
        $password = '0123456789';

        $match0 = $this->getMockMatchArray(0, 9, 3);
        $match1 = $this->getMockMatchArray(0, 3, 2);
        $match2 = $this->getMockMatchArray(4, 9, 1);
        $matches = [$match0, $match1, $match2];

        $result = $scoring->mostGuessableMatchSequence($password, $matches, true);

        $this->assertEquals(3, $result['guesses']);
        $this->assertEquals([$match0], $result['sequence']);

        $match0['guesses'] = 5;
        $matches[0] = $match0;

        $result = $scoring->mostGuessableMatchSequence($password, $matches, true);

        $this->assertEquals(4, $result['guesses']);
        $this->assertEquals([$match1, $match2], $result['sequence']);
    }

    public function testEstimateGuessesReturnsCachedGuessesWhenAvailable()
    {
        $match = ['guesses' => 1];
        $password = 'testpassword';

        $estimateGuessesMethod = $this->getEstimateGuessesReflectionMethod();

        $scoring = new Scoring(new EstimatorFactory());

        $this->assertEquals(1, $estimateGuessesMethod->invokeArgs($scoring, [$password, &$match]));
    }

    public function testEstimateGuessesDelegatesBasedOnPattern()
    {
        $estimatorFactory = new EstimatorFactory();
        $scoring = new Scoring($estimatorFactory);
        $dateEstimator = $estimatorFactory->create(EstimatorFactory::TYPE_DATE);

        $estimateGuessesMethod = $this->getEstimateGuessesReflectionMethod();

        $password = 'testpassword';
        $match = [
            'pattern' => 'date',
            'token' => '1977',
            'year' => 1977,
            'month' => 7,
            'day' => 14,
        ];
        $this->assertEquals(
            $dateEstimator->estimate($match),
            $estimateGuessesMethod->invokeArgs($scoring, [$password, &$match])
        );
    }

    protected function setUpUnitTest()
    {
        $mockEstimator = $this->getMockBuilder(
            AbstractEstimator::class
        )
            ->setMethods(['estimate'])
            ->getMock();
        $mockEstimator->expects($this->atLeastOnce())
            ->method('estimate')
            ->willReturn(1e3);

        $mockEstimatorFactory = $this->getMockBuilder(
            EstimatorFactory::class
        )
            ->setMethods(['create'])
            ->getMock();
        $mockEstimatorFactory->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($mockEstimator);

        $this->scoring = new Scoring($mockEstimatorFactory);
    }

    /**
     * @param int $i
     * @param int $j
     * @param int $guesses
     * @return array
     */
    protected function getMockMatchArray($i, $j, $guesses)
    {
        return [
            'i' => $i,
            'j' => $j,
            'guesses' => $guesses,
        ];
    }
<<<<<<< HEAD:test/ScoringTest.php

    /**
     * @return \ReflectionMethod
     */
    protected function getEstimateGuessesReflectionMethod()
    {
        $scoringReflection = new ReflectionClass('Zxcvbn\Scoring');
        $method = $scoringReflection->getMethod('estimateGuesses');
        $method->setAccessible(true);
        return $method;
    }

}
=======
}
>>>>>>> 2b87d1723f99e79df66e7b5b40dd319c4f249511:test/functional/ScoringTest.php
