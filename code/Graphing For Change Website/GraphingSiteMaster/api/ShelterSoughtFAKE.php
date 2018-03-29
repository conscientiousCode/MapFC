<?php

include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/distributions/WinterDistribution.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/distributions/ShelterWaitlistDistribution.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/Random.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/json/GoogleJsonFormatter.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/json/JsonValidator.php';



$shelterDesireRate = 1/10;
$fluctuationPerDay = 1/40;
$population = 1500;

$winterSol = new WinterDistribution($shelterDesireRate, $fluctuationPerDay, $population);

$waitlistProb = 1/8;
$waitlistPerterbation = 1/50;
$shelterWait = new ShelterWaitlistDistribution($waitlistProb, $waitlistPerterbation, $population);

$jsonFormatter = new GoogleJsonFormatter();
$jsonFormatter->addCol(array("name"=> "day", "type"=>"number"));//0
$jsonFormatter->addCol(array("name"=> "seeking", "type"=>"number"));//1
$jsonFormatter->addCol(array("name"=> "waiting", "type"=>"number"));//2


for($i = 1; $i <= 365; $i++){
    $row = array(
        0=> $i,
        1=> $winterSol->valueForDay($i),
        2=>$shelterWait->valueForDay($i) +20+10*random()
    );
    //echo $row[0]."\n".$row[1]."\n";
    $jsonFormatter->addRow($row);
}


echo $jsonFormatter->getJson();