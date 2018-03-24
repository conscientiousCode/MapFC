<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 12/03/18
 * Time: 9:52 PM
 */

include_once 'AnnualDistribution.php';

class SolsticeDistribution implements AnnualDistribution
{
    private $baseProb;
    private $upperProbPerterbation;
    private $N;//Size

    public function __construct($baseProbability, $upperProbabilityPerterbation, $population){
        $this->baseProb = $baseProbability;
        $this->upperProbPerterbation = $upperProbabilityPerterbation;
        $this->N = $population;
    }

    //Random number between 0 and 1
    protected function random(){
        return mt_rand()/mt_getrandmax();
    }

    //This distribution is bi modal with peaks ~= 1 at $x = 172, 355
    //Perterbation taken into account
    public function valueForDay($day)
    {
        if($day < 1 || $day > 365){
            throw new InvalidArgumentException("$day must be in [1,365]");
        }

        //(floor($N*($baseProb + random()*upperPerterb)*solsticeDayModifier))
        return floor($this->N*($this->baseProb + $this->random()*$this->upperProbPerterbation)*self::solsticeDistribution($day));
    }

    //Function ranges from ~1.0004 to ~2, with peaks at the summer and winter solstic, day 172, 355
    public static function solsticeDistribution($day){
        $winterSol = 355;
        $winterSolEarlyYear = -10;//this is for when $day is near 1
        $summerSol = 172;
        $spread = (1/1000);
        //These are essentially two bellcurves centered at the solstices
        $winterAmount = exp(-$spread*pow($day-$winterSol,2));
        $winterEarlyAmount = exp(-$spread*pow($day-$winterSolEarlyYear,2));
        $summerAmount = exp(-$spread*pow($day-$summerSol,2));

        return ($winterAmount+$winterEarlyAmount+$summerAmount) +1;

    }
}
