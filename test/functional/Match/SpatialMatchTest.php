<?php

namespace test\functional\Match;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\SpatialMatch;

class SpatialMatchTest extends TestCase
{
    /**
     * @var SpatialMatch
     */
    protected $spatialMatch;

    public function setUp()
    {
        $this->spatialMatch = new SpatialMatch();
    }

    public function testIsTesting()
    {
        // @todo remove
        $this->assertFalse(true);
    }
}
