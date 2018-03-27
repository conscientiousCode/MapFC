<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 26/03/18
 * Time: 11:34 PM
 */

include_once 'ShelterWaitlistDistribution.php';

class ShelterWaitlistDistributionTest extends PHPUnit_Framework_TestCase
{
    public function testValueOf(){
        $base = 1/20;
        $upper = 0.05;
        $pop = 1500;
        $winterDist = new ShelterWaitlistDistribution($base, $upper, $pop);

        try{

            $winterDist->valueForDay(0);
        }catch (InvalidArgumentException $e){
            self::assertTrue(true);
        }

        $min = 0;
        $max = 1.001*($base + $upper)*$pop;

        //echo $min."\n";
        //echo $max."\n\n";

        $minObserved = 1000;
        $maxObserved = 0;
        //Gross test, but we are dealing with probability
        for($i = 1; $i < 1000; $i++){
            $v = $winterDist->valueForDay(355);
            if($v <$minObserved){
                $minObserved = $v;
            }elseif($v > $maxObserved){
                $maxObserved = $v;
            }
        }

        self::assertTrue($min <= $minObserved);
        self::assertTrue($max >= $maxObserved);
        //echo "Min:\t".$minObserved."\tMax:\t".$maxObserved."\n";
    }

    function testSolsticeDistribution(){
        $min = 0;
        $max = 1.000001;

        $minObserved = 20;
        $maxObserved = 0;
        for($i = 1; $i < 365; $i++){
            $v = ShelterWaitlistDistribution::shelterWaitlistDistribution($i);
            if($v <$minObserved){
                $minObserved = $v;
            }elseif($v > $maxObserved){
                $maxObserved = $v;
            }
        }

        //echo "Min:\t".$minObserved."\tMax:\t".$maxObserved."\n";

        self::assertTrue($min <= $minObserved);
        self::assertTrue($max >= $maxObserved);

        //TODO: Figure out why the below is not generating close enough for small interval
        $tollerance = 0.005;
       // echo $maxObserved."\n";
       // echo ShelterWaitlistDistribution::shelterWaitlistDistribution(365);
        self::assertTrue(abs($maxObserved - ShelterWaitlistDistribution::shelterWaitlistDistribution(365)) < $tollerance);

    }

}
