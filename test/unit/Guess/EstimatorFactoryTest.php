<?php
namespace ZxcvbnPhp\Guess;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Guess\BruteForceEstimator;
use Zxcvbn\Guess\EstimatorFactory;

class EstimatorFactoryTest extends TestCase
{
    /**
     * @var EstimatorFactory
     */
    protected $estimatorFactory;

    public function setUp()
    {
        $this->estimatorFactory = new EstimatorFactory();
    }

    public function testCreatesBruteForceEstimator()
    {
        $this->assertInstanceOf(
            '\Zxcvbn\Guess\BruteForceEstimator',
            $this->estimatorFactory->create(EstimatorFactory::TYPE_BRUTE_FORCE)
        );
    }

    public function testCreatesDateEstimator()
    {
        $this->assertInstanceOf(
            '\Zxcvbn\Guess\DateEstimator',
            $this->estimatorFactory->create(EstimatorFactory::TYPE_DATE)
        );
    }

    public function testCreatesDictionaryEstimator()
    {
        $this->assertInstanceOf(
            '\Zxcvbn\Guess\DictionaryEstimator',
            $this->estimatorFactory->create(EstimatorFactory::TYPE_DICTIONARY)
        );
    }

    public function testCreatesRegexEstimator()
    {
        $this->assertInstanceOf(
            '\Zxcvbn\Guess\RegexEstimator',
            $this->estimatorFactory->create(EstimatorFactory::TYPE_REGEX)
        );
    }

    public function testCreatesRepeatEstimator()
    {
        $this->assertInstanceOf(
            '\Zxcvbn\Guess\RepeatEstimator',
            $this->estimatorFactory->create(EstimatorFactory::TYPE_REPEAT)
        );
    }

    public function testCreatesSequenceEstimator()
    {
        $this->assertInstanceOf(
            '\Zxcvbn\Guess\SequenceEstimator',
            $this->estimatorFactory->create(EstimatorFactory::TYPE_SEQUENCE)
        );
    }

    public function testCreatesSpatialEstimator()
    {
        $this->assertInstanceOf(
            '\Zxcvbn\Guess\SpatialEstimator',
            $this->estimatorFactory->create(EstimatorFactory::TYPE_SPATIAL)
        );
    }

    public function testThrowInvalidArgumentWhenInvalidTypePassed()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Estimator type "foo" does not exist');
        $this->estimatorFactory->create('foo');
    }
}
