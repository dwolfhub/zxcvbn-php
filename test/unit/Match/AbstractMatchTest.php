<?php
namespace ZxcvbnPhp\Match;

use PHPUnit\Framework\TestCase;
use Zxcvbn\Match\AbstractMatch;
use Zxcvbn\Match\DateMatch;

class AbstractMatchTest extends TestCase
{
    public function testSetRankedDictionaries()
    {
        // note: extends AbstractMatch
        $dateMatch = new DateMatch();
        $dateMatch->setRankedDictionaries([
            'foo' => ['abc', 'def'],
            'bar' => ['ghi', 'jkl'],
        ]);
        $this->assertArrayHasKey(
            'foo',
            $dateMatch->getRankedDictionaries()
        );
        $this->assertEquals([
            'abc' => 1,
            'def' => 2,
        ], $dateMatch->getRankedDictionaries()['foo']);
        $this->assertArrayHasKey(
            'bar',
            $dateMatch->getRankedDictionaries()
        );
        $this->assertEquals([
            'ghi' => 1,
            'jkl' => 2,
        ], $dateMatch->getRankedDictionaries()['bar']);
    }

    public function testSortByIAndJ()
    {
        $toSort = [
            [
                'i' => -1,
                'j' => 2,
            ],
            [
                'i' => 5,
                'j' => -10,
            ],
            [
                'i' => -1,
                'j' => 1,
            ],
            [
                'i' => -1,
                'j' => 2,
            ],
        ];
        usort($toSort, [AbstractMatch::class, 'sortByIAndJ']);
        $this->assertEquals([
            [
                'i' => -1,
                'j' => 1,
            ],
            [
                'i' => -1,
                'j' => 2,
            ],
            [
                'i' => -1,
                'j' => 2,
            ],
            [
                'i' => 5,
                'j' => -10,
            ],
        ], $toSort);
    }
}
