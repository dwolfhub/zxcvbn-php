<?php

namespace Zxcvbn\Match;

class SpatialMatch extends AbstractMatch
{
    /**
     * @var array
     */
    protected $graphs = [];

    /**
     * {@inheritdoc}
     */
    public function getMatches()
    {
        $matches = [];
        foreach ($this->graphs as $graphName => $graph) {
            $matches = $this->helper($graph, $graphName) + $matches;
        }

        usort($matches, [$this, 'sortByIAndJ']);

        return $matches;
    }

    /**
     * @param $graph
     * @param $graphName
     * @return array
     */
    protected function helper($graph, $graphName)
    {
        $matches = [];
        $i = 0;
        while ($i < strlen($this->password) - 1) {
            $j = $i + 1;
            $lastDirection = null;
            $turns = 0;
            if (
                in_array($graphName, ['qwerty', 'dvorak',])
                and preg_match('/[~!@#$%^&*()_+QWERTYUIOP{}|ASDFGHJKL:"ZXCVBNM<>?]/', substr($this->password, $i, 1))
            ) {
                // initial character is shifted
                $shiftedCount = 1;
            } else {
                $shiftedCount = 0;
            }

            while (true) {
                $prevChar = substr($this->password, $j - 1, 1);
                $found = false;
                $foundDirection = -1;
                $curDirection = -1;

                if (empty($graph[$prevChar]) === false) {
                    $adjacents = $graph[$prevChar];
                } else {
                    $adjacents = [];
                }

                # consider growing pattern by one character if j hasn't gone over the edge.
                if ($j < strlen($this->password)) {
                    $curChar = substr($this->password, $j, 1);
                    foreach ($adjacents as $adj) {
                        $curDirection += 1;
                        $adjIndex = strpos($adj, $curChar);
                        if ($adj and $adjIndex !== false) {
                            $found = true;
                            $foundDirection = $curDirection;
                            if ($adjIndex === 1) {
                                // index 1 in the adjacency means the key is shifted,
                                // 0 means unshifted: A vs a, % vs 5, etc.
                                // for example, 'q' is adjacent to the entry '2@'.
                                // @ is shifted w/ index 1, 2 is unshifted.
                                $shiftedCount += 1;
                            }
                            if ($lastDirection !== $foundDirection) {
                                // adding a turn is correct even in the initial case
                                // when last_direction is null:
                                // every spatial pattern starts with a turn.
                                $turns += 1;
                                $lastDirection = $foundDirection;
                            }

                            break;
                        }
                    }
                }
                // if the current pattern continued, extend j and try to grow again
                if ($found === true) {
                    $j += 1;
                } else {
                    // otherwise push the pattern discovered so far, if any...
                    if ($j - $i > 2) { // don't consider length 1 or 2 chains.
                        $matches[] = [
                            'pattern' => 'spatial',
                            'i' => $i,
                            'j' => $j - 1,
                            'token' => substr($this->password, $i, $j - $i),
                            'graph' => $graphName,
                            'turns' => $turns,
                            'shifted_count' => $shiftedCount,
                        ];
                    }
                    // ...and then start a new search for the rest of the password.
                    $i = $j;
                    break;
                }

            }
        }

        return $matches;
    }

    /**
     * @return array
     */
    public function getGraphs()
    {
        return $this->graphs;
    }

    /**
     * @param array $graphs
     */
    public function setGraphs(array $graphs)
    {
        $this->graphs = $graphs;
    }

}