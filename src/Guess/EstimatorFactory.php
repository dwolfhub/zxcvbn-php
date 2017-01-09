<?php
namespace Zxcvbn\Guess;

use InvalidArgumentException;

/**
 * Class EstimatorFactory
 * @package Zxcvbn\Guess
 */
class EstimatorFactory
{
    /**
     * @const string
     */
    const TYPE_BRUTE_FORCE = 'brute_force';

    /**
     * @const string
     */
    const TYPE_DATE = 'date';

    /**
     * @const string
     */
    const TYPE_DICTIONARY = 'dictionary';

    /**
     * @const string
     */
    const TYPE_REGEX = 'regex';

    /**
     * @const string
     */
    const TYPE_REPEAT = 'repeat';

    /**
     * @const string
     */
    const TYPE_SEQUENCE = 'sequence';

    /**
     * @const string
     */
    const TYPE_SPATIAL = 'spatial';

    /**
     * @param $type
     * @return AbstractEstimator
     */
    public function create($type)
    {
        if ($type === self::TYPE_BRUTE_FORCE) {
            return new BruteForceEstimator();
        } else if ($type === self::TYPE_DATE) {
            return new DateEstimator();
        } else if ($type === self::TYPE_DICTIONARY) {
            return new DictionaryEstimator();
        } else if ($type === self::TYPE_REGEX) {
            return new RegexEstimator();
        } else if ($type === self::TYPE_REPEAT) {
            return new RepeatEstimator();
        } else if ($type === self::TYPE_SEQUENCE) {
            return new SequenceEstimator();
        } else if ($type === self::TYPE_SPATIAL) {
            return new SpatialEstimator();
        } else {
            throw new InvalidArgumentException(sprintf('Estimator type %s does not exist.', $type));
        }
    }
}