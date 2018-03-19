<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 17/03/18
 * Time: 10:47 AM
 */

include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/distributions/WinterDistribution.php';

function waitListDistribution($day,$spread){
    $centre = 365;
    $centreNext = (-1)*(365-$centre);
    return exp(-$spread*pow($day-$centre,2)) + exp(-$spread*pow($day-$centreNext,2));
}

function random(){
    return mt_rand()/mt_getrandmax();
}

function waitList($day,$perterbation,$population, $spread){
    return floor(waitListDistribution($day,$spread)*($perterbation + random()*$population)*$population);
}

$shelterDesireRate = 1/10;
$fluctuationPerDay = 1/30;
$population = 1500;

$winterSol = new WinterDistribution($shelterDesireRate, $fluctuationPerDay, $population);

$rows = [];
$json =  $violenceJson = '{ "cols":[
        {"id":"","label":"Day","pattern":"","type":"number"},
        {"id":"","label":"Seeking","pattern":"","type":"number"},
        {"id":"","label":"Wait Lister","pattern":"","type":"number"},
    ], "rows":[';

$comma = ",";

for($i = 1; $i <= 365; $i++){
    $rows[$i] = array(
        "seeking"=> $winterSol->valueForDay($i),
        "waiting"=> waitList($i, 1/500, $population, 20)
    );

    $json = $json.'{"c":[{"v":'.$i.'},{"v":'.$rows[$i]["seeking"].'},{"v":'.$rows[$i]["waiting"].'}]}'.$comma."\n";
    if ($i == 364){
        $comma = "";
    }
}

$json = $json.']}';

echo $json;