<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 24/03/18
 * Time: 6:49 PM
 */

function random(){
    return mt_rand() / mt_getrandmax();
}

//0 to max
function randomMax($max){
    if($max < 0){
        throw new InvalidArgumentException("Max must be positive to generate 0 to max");
    }
    return random()*$max;
}

function randomInRange($min, $max){
    $extendRange = 0;
    if($min < 0){
        $extendRange = randomMax(abs($min));
    }
    return $min + randomMax($max) + $extendRange;
}


