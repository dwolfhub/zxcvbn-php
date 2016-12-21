<?php

namespace ZxcvbnPhp\Match;

/**
 * Class AbstractMatch
 * @package Match
 */
abstract class AbstractMatch
{
    /**
     * @var string
     */
    protected $password;

    /**
     * AbstractMatch constructor.
     * @param $password
     */
    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * @return array
     */
    abstract public function getMatches();
}