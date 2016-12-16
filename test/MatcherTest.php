<?php

namespace ZxcvbnPhp\Test;

use ZxcvbnPhp\Match;

class MatcherTest extends \PHPUnit_Framework_TestCase
{

    public function testGetMatches()
    {
        $this->markTestSkipped('Temporarily skipping');

        $matcher = new Matcher();
        $matches = $matcher->getMatches("jjj");
        $this->assertSame('repeat', $matches[0]->pattern, "Pattern incorrect");
        $this->assertCount(1, $matches);

        $matches = $matcher->getMatches("jjjjj");
        $this->assertSame('repeat', $matches[0]->pattern, "Pattern incorrect");
    }
}