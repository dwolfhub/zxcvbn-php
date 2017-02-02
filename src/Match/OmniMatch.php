<?php

namespace Zxcvbn\Match;

/**
 * Class OmniMatch
 * @package Zxcvbn\Match
 */
class OmniMatch extends AbstractMatch
{
    /**
     * {@inheritdoc}
     */
    public function getMatches()
    {
        $matchFactory = new MatchFactory();

        $matches = [
            $matchFactory->create(MatchFactory::TYPE_DATE, $this->password),
            $matchFactory->create(MatchFactory::TYPE_DICTIONARY, $this->password),
            $matchFactory->create(MatchFactory::TYPE_L33T, $this->password),
            $matchFactory->create(MatchFactory::TYPE_REGEX, $this->password),
            $matchFactory->create(MatchFactory::TYPE_REPEAT, $this->password),
            $matchFactory->create(MatchFactory::TYPE_REVERSE_DICTIONARY, $this->password),
            $matchFactory->create(MatchFactory::TYPE_SEQUENCE, $this->password),
            $matchFactory->create(MatchFactory::TYPE_SPATIAL, $this->password),
        ];

        $results = [];
        foreach ($matches as $match) {
            $results = $match->getMatches() + $results;
        }

        usort($results, [$this, 'sortByIAndJ']);

        return $results;
    }
}