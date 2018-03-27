<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 26/03/18
 * Time: 11:06 PM
 */

include_once 'AnnualDistribution.php';
include_once __DIR__.'/../Random.php';

class ShelterWaitlistDistribution implements AnnualDistribution
{
    private $baseProb;
    private $upperProbPerterbation;
    private $N;//Size

    public function __construct($baseProbability, $upperProbabilityPerterbation, $population){
        $this->baseProb = $baseProbability;
        $this->upperProbPerterbation = $upperProbabilityPerterbation;
        $this->N = $population;
    }

    public function valueForDay($day)
    {

        if($day < 1 || $day > 365){
            throw new InvalidArgumentException("$day must be in [1,365]");
        }

        //(floor($N*($baseProb + random()*upperPerterb)*solsticeDayModifier))
        return floor($this->N*($this->baseProb + random()*$this->upperProbPerterbation)*self::shelterWaitlistDistribution($day));
    }

    //bellcurve centered at the winter solstice, ranges from ~0 to ~1
    public static function shelterWaitlistDistribution($day){
        $centre = 365;
        $spread = 1/500;
        $centreNext = (-1)*(365-$centre);
        return exp(-$spread*pow($day -$centre,2)) + exp(-$spread*pow($day-$centreNext,2));


    }

}