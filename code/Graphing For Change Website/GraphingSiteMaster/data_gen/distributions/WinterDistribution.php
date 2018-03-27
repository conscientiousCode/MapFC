<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 17/03/18
 * Time: 9:38 AM
 */
include_once 'AnnualDistribution.php';
include_once __dir__.'/../Random.php';

class WinterDistribution implements AnnualDistribution
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
        return floor($this->N*($this->baseProb + random()*$this->upperProbPerterbation)*self::winterDistribution($day));
    }

    //bellcurve centered at the winter solstice, ranges from 1 to 2
    public static function winterDistribution($day){
        $winterSol = 355;
        $winterSolEarlyYear = -10;//this is for when $day is near 1
        $spread = (1/1000);
        $winterAmount = exp(-$spread*pow($day-$winterSol,2));
        $winterEarlyAmount = exp(-$spread*pow($day-$winterSolEarlyYear,2));

        return ($winterAmount+$winterEarlyAmount) +1;

    }

}