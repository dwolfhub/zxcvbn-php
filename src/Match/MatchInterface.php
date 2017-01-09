<?php

namespace Zxcvbn\Match;

/**
 * Interface MatchInterface
 * @package Match
 */
interface MatchInterface
{
    /**
     * @return array
     */
    public function getMatches();
}