<?php

namespace Zxcvbn;

use Match\MatchFactory;
use Zxcvbn\Guess\EstimatorFactory;
use Zxcvbn\Match\AbstractMatch;
use Zxcvbn\Match\DataProvider\FrequencyLists;
use Zxcvbn\Match\OmniMatch;

class Zxcvbn
{
    /**
     * @var AbstractMatch
     */
    protected $omniMatch;

    /**
     * @var Scoring
     */
    protected $scoring;

    /**
     * @var TimeEstimates
     */
    protected $timeEstimates;

    /**
     * @var Feedback
     */
    protected $feedback;

    /**
     * Zxcvbn constructor.
     *
     * @param AbstractMatch $omniMatch
     * @param Scoring $scoring
     * @param TimeEstimates $timeEstimates
     * @param Feedback $feedback
     */
    public function __construct(
        AbstractMatch $omniMatch,
        Scoring $scoring,
        TimeEstimates $timeEstimates,
        Feedback $feedback
    )
    {
        $this->omniMatch = $omniMatch;
        $this->scoring = $scoring;
        $this->timeEstimates = $timeEstimates;
        $this->feedback = $feedback;
    }

    /**
     * Calculates password strength, called statically
     *
     * @param string $password Password to measure.
     * @param array $userInputs Optional user inputs.
     * @return array
     */
    public static function passwordStrength($password, array $userInputs = [])
    {
        $matchFactory = new MatchFactory();
        $omniMatch = $matchFactory->create(
            MatchFactory::TYPE_OMNI,
            $password
        );

        $zxcvbn = new static(
            $omniMatch,
            new Scoring(new EstimatorFactory()),
            new TimeEstimates(),
            new Feedback()
        );

        return $zxcvbn->calculateStrength($password, $userInputs);
    }

    /**
     * Calculates password strength
     *
     * @param string $password Password to measure.
     * @param array $userInputs Optional user inputs.
     * @return array
     */
    public function calculateStrength($password, array $userInputs = [])
    {
        $start = microtime(true);

        $sanitizedInputs = [];
        foreach ($userInputs as $userInput) {
            $sanitizedInputs[] = strtolower((string)$userInput);
        }
        $this->omniMatch->addRankedDictionary('user_inputs', $sanitizedInputs);

        $matches = $this->omniMatch->getMatches();
        $result = $this->scoring->mostGuessableMatchSequence($password, $matches, empty($matches) === false);

        $result['calc_time'] = microtime(true) - $start;
        $result += $this->timeEstimates->estimateAttackTimes($result['guesses']);

        $result['feedback'] = $this->feedback->getFeedback($result['score'], $result['sequence']);

        return $result;
    }

}