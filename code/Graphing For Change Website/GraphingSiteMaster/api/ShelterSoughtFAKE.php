<?php

include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/distributions/WinterDistribution.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/json/GoogleJsonFormatter.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/json/JsonValidator.php';

function waitListDistribution($day,$spread){
    $centre = 365;
    $centreNext = (-1)*(365-$centre);
    return exp(-$spread*pow($day -$centre,2)) + exp(-$spread*pow($day-$centreNext,2));
}

function random(){
    return mt_rand()/mt_getrandmax();
}

function waitList($day,$perterbation,$population, $spread){
    return floor(waitListDistribution($day,$spread)*(0.5+ $perterbation*random()*0.5)*$population);
}

$shelterDesireRate = 1/10;
$fluctuationPerDay = 1/40;
$population = 1500;

$winterSol = new WinterDistribution($shelterDesireRate, $fluctuationPerDay, $population);

$jsonFormatter = new GoogleJsonFormatter();
$jsonFormatter->addCol(array("name"=> "day", "type"=>"number"));//0
$jsonFormatter->addCol(array("name"=> "seeking", "type"=>"number"));//1
$jsonFormatter->addCol(array("name"=> "waiting", "type"=>"number"));//2


for($i = 1; $i <= 365; $i++){
    $row = array(
        0=> $i,
        1=> $winterSol->valueForDay($i),
        2=> waitList($i, 1/3, $population/5, 1/500) + 20+10*random()
    );
    //echo $row[0]."\n".$row[1]."\n";
    $jsonFormatter->addRow($row);
}


echo $jsonFormatter->getJson();