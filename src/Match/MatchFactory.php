<?php

namespace Match;

use InvalidArgumentException;
use Zxcvbn\Match\AbstractMatch;
use Zxcvbn\Match\DataProvider\AdjacencyGraphs;
use Zxcvbn\Match\DateMatch;
use Zxcvbn\Match\DictionaryMatch;
use Zxcvbn\Match\L33tMatch;
use Zxcvbn\Match\OmniMatch;
use Zxcvbn\Match\RegexMatch;
use Zxcvbn\Match\RepeatMatch;
use Zxcvbn\Match\ReverseDictionaryMatch;
use Zxcvbn\Match\SequenceMatch;
use Zxcvbn\Match\SpatialMatch;
use Zxcvbn\Scoring;

/**
 * Class MatchFactory
 * @package Match
 */
class MatchFactory
{

    /**
     * @const string TYPE_DATE
     */
    const TYPE_DATE = 'date';

    /**
     * @const string TYPE_DICTIONARY
     */
    const TYPE_DICTIONARY = 'dictionary';

    /**
     * @const string TYPE_L33T
     */
    const TYPE_L33T = 'l33t';

    /**
     * @const string TYPE_OMNI
     */
    const TYPE_OMNI = 'omni';

    /**
     * @const string TYPE_REGEX
     */
    const TYPE_REGEX = 'regex';

    /**
     * @const string TYPE_REPEAT
     */
    const TYPE_REPEAT = 'repeat';

    /**
     * @const string TYPE_REVERSE_DICTIONARY
     */
    const TYPE_REVERSE_DICTIONARY = 'reverse_dictionary';

    /**
     * @const string TYPE_SEQUENCE
     */
    const TYPE_SEQUENCE = 'sequence';

    /**
     * @const string TYPE_SPATIAL
     */
    const TYPE_SPATIAL = 'spatial';

    /**
     * @param string $type
     * @param string $password
     * @return AbstractMatch
     */
    public function create($type, $password)
    {
        if ($type === self::TYPE_DATE) {
            $match = new DateMatch();

        } else if ($type === self::TYPE_DICTIONARY) {
            $match = new DictionaryMatch();

        } else if ($type === self::TYPE_L33T) {
            $match = new L33tMatch();
            $match->setL33tTable(L33tMatch::DEFAULT_L33T_TABLE); // @todo refactor?
            $match->setDictionaryMatch(
                $this->create(self::TYPE_DICTIONARY, $password)
            );

        } else if ($type === self::TYPE_OMNI) {
            $match = new OmniMatch();

        } else if ($type === self::TYPE_REGEX) {
            $match = new RegexMatch();

        } else if ($type === self::TYPE_REPEAT) {
            $match = new RepeatMatch();
            $match->setScoring(new Scoring($password));
            $match->setOmniMatch(
                $this->create(self::TYPE_OMNI, $password)
            );

        } else if ($type === self::TYPE_REVERSE_DICTIONARY) {
            $match = new ReverseDictionaryMatch();
            $match->setDictionaryMatch(
                $this->create(self::TYPE_DICTIONARY, $password)
            );

        } else if ($type === self::TYPE_SEQUENCE) {
            $match = new SequenceMatch();

        } else if ($type === self::TYPE_SPATIAL) {
            $match = new SpatialMatch();
            $match->setGraphs(AdjacencyGraphs::getData());

        } else {
            throw new InvalidArgumentException(sprintf('%s is not a valid Match type', $type));

        }

        $match->setPassword($password);

        return $match;
    }
}