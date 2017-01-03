<?php

namespace Zxcvbn\Match;

/**
 * Class L33tMatch
 * @package Zxcvbn\Match
 */
class L33tMatch extends AbstractMatch
{
    /**
     * @var array
     */
    protected $l33tTable;

    /**
     * @var AbstractMatch
     */
    protected $dictionaryMatch;


    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        // todo refactor this
        $this->l33tTable = [
            'a' => ['4', '@'],
            'b' => ['8'],
            'c' => ['(', '{', '[', '<'],
            'e' => ['3'],
            'g' => ['6', '9'],
            'i' => ['1', '!', '|'],
            'l' => ['1', '|', '7'],
            'o' => ['0'],
            's' => ['$', '5'],
            't' => ['+', '7'],
            'x' => ['%'],
            'z' => ['2'],
        ];

    }

    /**
     * {@inheritdoc}
     */
    public function getMatches()
    {
        // todo refactor this
        $this->dictionaryMatch = new DictionaryMatch($this->password);

        $matches = [];

        foreach ($this->enumerateL33tSubs() as $sub) {
            if (!count($sub)) {
                break;
            }

            $subbedPassword = $this->translate($sub);
            $this->dictionaryMatch->setPassword($subbedPassword);
            foreach ($this->dictionaryMatch->getMatches() as $match) {
                $token = substr($this->password, $match['i'], $match['j'] - $match['i'] + 1);
                if (strtolower($token) === $match['matched_word']) {
                    // only return the matches that contain an actual substitution
                    continue;
                }

                // subset of mappings in sub that are in use for this match
                $matchSub = [];
                foreach ($sub as $subbedChr => $chr) {
                    if (strpos($token, $subbedPassword) !== false) {
                        $matchSub[$subbedChr] = $chr;
                    }
                }
                $match['l33t'] = true;
                $match['token'] = $token;
                $match['sub'] = $matchSub;

                $subDisplays = [];
                foreach ($matchSub as $k => $v) {
                    $subDisplays[] = sprintf('%s -> %s', $k, $v);
                }
                $match['sub_display'] = implode(', ', $subDisplays);
                array_push($matches, $match);
            }
        }

        $matches = array_filter($matches, function ($item) {
            return strlen($item['token']) > 1;
        });

        usort($matches, [$this, 'sortByIAndJ']);

        return $matches;
    }

    /**
     * @return array
     */
    public function getL33tTable()
    {
        return $this->l33tTable;
    }

    /**
     * @param array $l33tTable
     */
    public function setL33tTable(array $l33tTable)
    {
        $this->l33tTable = $l33tTable;
    }

    /**
     * @param $string
     * @param $chrMap
     * @return string
     */
    protected function translate($chrMap)
    {
        $chars = [];
        foreach (str_split($this->password) as $char) {
            if (!empty($chrMap[$char])) {
                array_push($chars, $chrMap[$char]);
            } else {
                array_push($chars, $char);
            }
        }

        return implode('', $chars);
    }

    /**
     * @param $table
     * @return array
     */
    protected function relevantL33tSubtable($table)
    {
        $passwordChars = [];
        foreach (str_split($this->password) as $char) {
            $passwordChars[$char] = true;
        }

        $subTable = [];
        foreach ($table as $letter => $subs) {
            $relevantSubs = [];
            foreach ($subs as $sub) {
                if (in_array($sub, $passwordChars)) {
                    $relevantSubs[] = $sub;
                }
            }
            if (!empty($relevantSubs)) {
                $subTable[$letter] = $relevantSubs;
            }
        }

        return $subTable;
    }

    /**
     * @param array $table
     * @return array
     */
    protected function enumerateL33tSubs()
    {
        $keys = array_keys($this->l33tTable);
        $subs = [[]];

        $subs = $this->helper($keys, $subs);
        $subDicts = []; // convert from assoc lists to dicts
        foreach ($subs as $sub) {
            $subDict = [];
            foreach ($sub as $l33tChr => $chr) {
                $subDict[$l33tChr] = $chr;
            }
            array_push($subDicts, $subDict);
        }

        return $subDicts;
    }

    /**
     * @param $subs
     */
    protected function deDup($subs)
    {
        $deduped = [];
        $members = [];
        foreach ($subs as $sub) {
            $assoc = [];
            foreach ($sub as $k => $v) {
                $assoc[] = [$v, $k];
            }
            sort($assoc);
            $labels = [];
            foreach ($assoc as $k => $v) {
                $labels[] = $k . ',' . $v;
            }
            $label = implode('-', $labels);
            if (!array_key_exists($label, $members)) {
                $members[$label] = true;
                array_push($deduped, $sub);
            }
        }

        return $deduped;
    }

    /**
     * @param $keys
     * @param $subs
     */
    protected function helper($keys, $subs)
    {
        if (empty($keys)) {
            return $subs;
        }

        $firstKey = $keys[0];
        $restKeys = array_slice($keys, 1);
        $nextSubs = [];
        foreach ($this->l33tTable[$firstKey] as $l33tChr) {
            foreach ($subs as $sub) {
                $dupL33tIndex = -1;
                for ($i = 0; $i < strlen($sub); $i++) {
                    if ($sub[$i][0] == $l33tChr) {
                        $dupL33tIndex = $i;
                        break;
                    }
                }
                if ($dupL33tIndex == -1) {
                    $subExtension = str_split($sub);
                    array_push($subExtension, [$l33tChr, $firstKey]);
                    array_push($nextSubs, $subExtension);
                } else {
                    $subAlternative = $sub;
                    array_splice($subAlternative, $dupL33tIndex, 1);
                    array_push($subAlternative, [$l33tChr, $firstKey]);
                    array_push($nextSubs, $sub);
                    array_push($nextSubs, $subAlternative);
                }
            }
        }

        $subs = $this->deDup($nextSubs);

        return $this->helper($restKeys, $subs);
    }

}