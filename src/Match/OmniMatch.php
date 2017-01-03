<?php

namespace Zxcvbn\Match;

use Zxcvbn\Match\DataProvider\AdjacencyGraphs;
use Zxcvbn\Scoring;

class OmniMatch extends AbstractMatch
{
    public function getMatches()
    {
        // initialize matchers
        // todo refactor to remove coupling?
        $matches = [];

        $matches[] = new DateMatch();
        $dictionaryMatch = new DictionaryMatch();
        $matches[] = $dictionaryMatch;
        $matches[] = new RegexMatch();

        $l33tMatch = new L33tMatch();
        $l33tMatch->setL33tTable(L33tMatch::DEFAULT_L33T_TABLE);
        $l33tMatch->setDictionaryMatch($dictionaryMatch);
        $matches[] = $l33tMatch;

        $repeatMatch = new RepeatMatch();
        $repeatMatch->setScoring(new Scoring($this->password));
        $repeatMatch->setOmniMatch($this);
        $matches[] = $repeatMatch;

        $reverseDictionaryMatch = new ReverseDictionaryMatch();
        $reverseDictionaryMatch->setDictionaryMatch($dictionaryMatch);
        $matches[] = $reverseDictionaryMatch;

        $matches[] = new SequenceMatch();
        $spatialMatch = new SpatialMatch();
        $spatialMatch->setGraphs(AdjacencyGraphs::getData());
        $matches[] = $spatialMatch;

        $results = [];

        /** @var AbstractMatch $match */
        foreach ($matches as $match) {
            $results = $match->getMatches() + $results;
        }
        usort($results, [$this, 'sortByIAndJ']);

        return $results;
    }
}