<?php

namespace ZxcvbnPhp\Test\Matchers;

use ZxcvbnPhp\Match\YearMatch;

class YearTest extends \PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $this->markTestSkipped('not ready for testing');
        $password = 'password';
        $matches = YearMatch::match($password);
        $this->assertEmpty($matches);

        $password = '1900';
        $matches = YearMatch::match($password);
        $this->assertCount(1, $matches);
        $this->assertSame($password, $matches[0]->token, "Token incorrect");
        $this->assertSame($password, $matches[0]->password, "Password incorrect");

        $password = 'password1900';
        $matches = YearMatch::match($password);
        $this->assertCount(1, $matches);
        $this->assertSame("1900", $matches[0]->token, "Token incorrect");
    }

    public function testEntropy()
    {
        $this->markTestSkipped('not ready for testing');
        $password = '1900';
        $matches = YearMatch::match($password);
        $this->assertEquals(log(119, 2), $matches[0]->getEntropy());
    }
}