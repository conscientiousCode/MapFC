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
        $baseProb = $baseProbability;
        $upperProbPerterbation = $upperProbabilityPerterbation;
        $N = $population;
    }

    private function random(){
        mt_rand()/
    }

    //This distribution is bi modal with peaks ~= 1 at $x = 172, 355
    public function valueFor($x)
    {
        if($x < 1 || $x > 365){
            throw new InvalidArgumentException("$x must be in [1,365]");
        }
    }
}
