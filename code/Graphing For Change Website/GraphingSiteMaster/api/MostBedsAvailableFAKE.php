<?php

include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/json/GoogleJsonFormatter.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/json/JsonValidator.php';

function random(){
    return mt_rand()/mt_getrandmax();
}

//0 to $upperBound -1
function randomUpTo($upperBound){
    return floor(random()*$upperBound);
}

//Create the max number of beds an organization supports, randomly
function generateBedCapacities($upperBound, $lengthOfArray){
    $totalBeds = [];
    for($i = 0; $i < $lengthOfArray; $i++){
        do{
            $totalBeds[$i] = randomUpTo($upperBound+1);
        }while($totalBeds[$i] == 0);
    }
    return $totalBeds;
}

//Mean is center, with probabilities decrease as we move away from center
function generateBedsRemaining($totalBeds){
    $bedsRemaining = [];
    for($i = 0; $i<count($totalBeds); $i++){
        $bedsRemaining[$i] = randomUpTo(randomUpTo($totalBeds[$i] +1)+1);
    }
    return $bedsRemaining;
}


//gross but simple sort for low numbers of organizations, n^2 time
//all parameters are arrays of the same length
function sortByMostAvailable($orgs, $totalBeds, $availableBeds){
    if(!(count($orgs) == count($totalBeds) && count($orgs) == count($availableBeds))){
        throw new InvalidArgumentException("All arrays must be of the same length");
    }

    for($i = 0; $i < count($totalBeds); $i++){
        for($j = 1; $j <=$i; $j++){
            if($availableBeds[$j] > $availableBeds[$j-1]){
                swap($availableBeds, $j, $j-1);
                swap($totalBeds, $j, $j-1);
                swap($orgs, $j, $j-1);
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

$MAX_BEDS_ALLOWED = 50;
$totalBeds = generateBedCapacities($MAX_BEDS_ALLOWED, count($fakeOrgs));
$availableBeds = generateBedsRemaining($totalBeds);

sortByMostAvailable($fakeOrgs, $totalBeds, $availableBeds);

for($i = 0; $i < count($fakeOrgs); $i++){
$jsonGen->addRow(array(
    0=>$fakeOrgs[$i],
    1=>$totalBeds[$i],
    2=>$availableBeds[$i]
));}


echo $jsonGen->getJson();