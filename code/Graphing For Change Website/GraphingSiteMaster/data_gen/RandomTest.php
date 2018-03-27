<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 26/03/18
 * Time: 8:32 PM
 */

include_once __dir__.'/Random.php';

//WARNING, THESE TESTS ARE STATISTICAL AND ARE ONLY INTENDED TO CAT HEAVY BIAS AWAY FROM
//THE EXPECTED AVERAGE, OR TO FIND VALUES BEYOND WHAT THEY SHOULD BE.

class RandomTest extends PHPUnit_Framework_TestCase
{

    private $THRESH_HOLD = 0.10;

    //In 1000 values ensure that all values are within the bounds
    //Ensure that the average value falls within 10% of 50%;
    public function testRandom(){
        $iterMax = 1000;
        $values = [];
        $sum = 0;
        $min = 0;
        $max = 1;
        //In 1000 values ensure that all values are within the bounds
        for($i = 0; $i< $iterMax; $i++){
            $values[$i]= random();
            if($values[$i] < $min){
                $min = $values[$i];
            }elseif($max < $values[$i]){
                $max = $values[$i];
            }
            $sum += $values[$i];
        }

        self::assertTrue($min <= 0);
        self::assertTrue($max >= 1);
        //Ensures that the average value falls within 10% of the expected.
        self::assertTrue(abs(($sum/$iterMax) - 0.5) <= $this->THRESH_HOLD);
    }

    public function testRandomMax(){
        $iterMax = 1000;
        $REQ_MAX = 1500;
        $values = [];
        $sum = 0;
        $min = 0;
        $max = $REQ_MAX;
        //In 1000 values ensure that all values are within the bounds
        for($i = 0; $i< $iterMax; $i++){
            $values[$i]= randomMax($REQ_MAX);
            if($values[$i] < $min){
                $min = $values[$i];
            }elseif($max < $values[$i]){
                $max = $values[$i];
            }
            $sum += $values[$i];
        }

        self::assertTrue($min <= 0);
        self::assertTrue($max >= $REQ_MAX);
        //Ensures that the average value falls within 10% of the expected.
        self::assertTrue(abs(($sum/$iterMax) - ($REQ_MAX/2)) <= $this->THRESH_HOLD*$REQ_MAX);
    }

    public function testRandomInRange(){
        $iterMax = 1000;
        //Before altering these values, see the TODO: Below
        $REQ_MAX = 1500;
        $REQ_MIN = -1000;
        $values = [];
        $sum = 0;
        $min = $REQ_MIN;
        $max = $REQ_MAX;
        //In 1000 values ensure that all values are within the bounds
        for($i = 0; $i< $iterMax; $i++){
            $values[$i]= randomInRange($REQ_MIN,$REQ_MAX);
            if($values[$i] < $min){
                $min = $values[$i];
            }elseif($max < $values[$i]){
                $max = $values[$i];
            }
            $sum += $values[$i];
        }

        self::assertTrue($min <= $REQ_MIN);
        self::assertTrue($max >= $REQ_MAX);
        //Ensures that the average value falls within 10% of the expected.
        $EXPECTED_AVG = ($REQ_MIN + $REQ_MAX)/2;
        /*echo $EXPECTED_AVG;
        echo "\n".($sum/$iterMax)."\n";
        echo ($this->THRESH_HOLD+0.05)*$EXPECTED_AVG;*/
        //TODO: If the closer $REQ_MIN = -$REQ_MAX, the smaller the error can be. Make this more robust.
        //The plus 0.05 is a kind of hack right now.
        self::assertTrue(abs(($sum/$iterMax) - $EXPECTED_AVG) <= ($this->THRESH_HOLD+0.05)*$EXPECTED_AVG);
    }

}
