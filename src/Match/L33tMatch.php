<?php

namespace ZxcvbnPhp\Match;

/**
 * Class L33tMatch
 * @package ZxcvbnPhp\Match
 */
class L33tMatch extends AbstractMatch
{
    /**
     * {@inheritdoc}
     */
    public function getMatches()
    {
        $matches = [];

        foreach ($this->enumerateL33tSubs($table) as $sub) {

        }
    }

    /**
     * @param array $table
     * @return array
     */
    protected function enumerateL33tSubs($table)
    {
        $keys = array_keys($table);
        $subs = [[]];

        $subs = $this->helper($keys, $subs, $table);
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
                $labels[] = $k.','.$v;
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
    protected function helper($keys, $subs, $table)
    {
        if (empty($keys)) {
            return $subs;
        }

        $firstKey = $keys[0];
        $restKeys = array_slice($keys, 1);
        $nextSubs=[];
        foreach ($table[$firstKey] as $l33tChr) {
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

        return $this->helper($restKeys, $subs, $table);
    }

}