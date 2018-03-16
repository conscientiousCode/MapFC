<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 12/03/18
 * Time: 9:57 PM
 */


include 'SolsticeDistribution.php';

class SolsticeDistributionTest extends PHPUnit_Framework_TestCase
{

    public function testValueOf(){
        $solDist = new SolsticeDistribution();

        try{

            $solDist::valueFor(0);
        }catch (InvalidArgumentException $e){
            self::assertTrue(true);
        }
    }
}


?>
