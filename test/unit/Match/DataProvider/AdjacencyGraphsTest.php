<?php

namespace unit\Match\DataProvider;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\DataProvider\AdjacencyGraphs;

class AdjacencyGraphsTest extends TestCase
{
    public function testProvidesProperLists()
    {
        $graphs = AdjacencyGraphs::getData();

        $this->assertArrayHasKey('qwerty', $graphs);
        $this->assertInternalType('array', $graphs['qwerty']);

        $this->assertArrayHasKey('dvorak', $graphs);
        $this->assertInternalType('array', $graphs['dvorak']);

        $this->assertArrayHasKey('keypad', $graphs);
        $this->assertInternalType('array', $graphs['keypad']);

        $this->assertArrayHasKey('mac_keypad', $graphs);
        $this->assertInternalType('array', $graphs['mac_keypad']);
    }
}
