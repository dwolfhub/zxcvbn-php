<?php
namespace ZxcvbnPhp\test\Matcher\DataProvider;

use ZxcvbnPhp\Match\DataProvider\FrequencyLists;

class FrequencyListsTest extends \PHPUnit_Framework_TestCase
{
    public function testIsProvidingData()
    {
        $data = FrequencyLists::getData();
        $this->assertCount(6, $data);
        $this->assertArrayHasKey('english_wikipedia', $data);
        $this->assertArrayHasKey('female_names', $data);
        $this->assertArrayHasKey('male_names', $data);
        $this->assertArrayHasKey('passwords', $data);
        $this->assertArrayHasKey('us_tv_and_film', $data);
    }
}