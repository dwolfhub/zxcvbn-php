<?php

namespace functional;

use PHPUnit_Framework_TestCase;
use Zxcvbn\Zxcvbn;

class ZxcvbnTest extends PHPUnit_Framework_TestCase
{
    public function testIsTesting()
    {
        $this->markTestIncomplete();

        // @todo fix dictionary match first
        $this->assertEquals(
            [],
            Zxcvbn::passwordStrength('testpassword', [])
        );
    }
}