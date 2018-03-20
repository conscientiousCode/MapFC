<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 20/03/18
 * Time: 12:12 AM
 */

include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/json/GoogleJsonFormatter.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/json/JsonValidator.php';

function random(){
    return mt_rand()/mt_getrandmax();
}

//0 to $upperBound -1
function randomUpTo($upperBound){
    return floor(random()*$upperBound);
}

function generateBedCapacities($upperBound, $lengthOfArray){
    $totalBeds = [];
    for($i = 0; $i < $lengthOfArray; $i++){
        do{
            $totalBeds[$i] = randomUpTo($upperBound+1);
        }while($totalBeds == 0);
    }
    return $totalBeds;
}

//Mean is center, with probabilities decrease as we move away from center
function generateBedsRemaining($totalCapacity){
    return randomUpTo(randomUpTo($totalCapacity +1)+1);
}


//gross but simple sort for low numbers of organizations, n^2 time
//all parameters are arrays of the same length
function sortByMostAvailable($orgs, $totalBeds, $availableBeds){
    if(!(count($orgs) == count($totalBeds) && count($orgs) == count($availableBeds))){
        throw new InvalidArgumentException("All arrays must be of the same length");
    }

    for($i = 0; $i < count($totalBeds); $i++){
        for($j = 1; $j <=$i; $j++){
            if($availableBeds[$j] < $availableBeds[$j-1]){
                swap
            }
        }
    }
}

function swap($array, $i, $j){
    $temp = $array[$i];
    $array[$i] = $array[$j];
    $array[$j] = $array[$i];
}

$fakeOrgs = array(
    0=>"Comfy Beds R Us",
    1=>"The Hearth",
    2=>"Strong Roofs",
    3=>"Bed and Breakfast",
    4=>"Dusk Till Dawn",
    5=>"A Home Until A Home",
    6=>"Function over Form",
    7=>"The Hay Loft",
    8=>"Wayfarer's",
    9=>"The Hub"
);

$jsonGen = new GoogleJsonFormatter();

$jsonGen->addCol(array("name"=>"Organization","type"=>"string"));
$jsonGen->addCol(array("name"=>"Total Beds","type"=>"number"));
$jsonGen->addCol(array("name"=>"Beds Available","type"=>"number"));



