<?php

namespace functional;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Zxcvbn;

class ZxcvbnTest extends TestCase
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