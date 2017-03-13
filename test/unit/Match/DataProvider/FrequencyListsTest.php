<?php

namespace unit\test\Match\DataProvider;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\DataProvider\FrequencyLists;

class FrequencyListsTest extends TestCase
{
    public function testProvidesProperLists()
    {
        $lists = FrequencyLists::getData();

        $this->assertArrayHasKey('english_wikipedia', $lists);
        $this->assertArrayHasKey('female_names', $lists);
        $this->assertArrayHasKey('male_names', $lists);
        $this->assertArrayHasKey('passwords', $lists);
        $this->assertArrayHasKey('surnames', $lists);
        $this->assertArrayHasKey('us_tv_and_film', $lists);
    }
}
