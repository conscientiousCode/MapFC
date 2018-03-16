
<?php
//Including the db connection object
//You'll probably have to play around with this to get it to work
//with different operating systems and file/server structures
//include_once $_SERVER["DOCUMENT_ROOT"].'/api/config/dbCreds.php';
include_once $_SERVER["DOCUMENT_ROOT"].'/data_gen/distributions/SolsticeDistribution.php';
//Handle AJAX request and POST data here
//Check if there's available data in the POST variable
//under the legitimate key
if(true) {//isset($_POST["req"])
    //Ensuring stmt var is null before preparing
    $stmt = null;

    $violencePerPerson = 1/200;
    $violencePerterbation = 1/250;
    $population = 1500;
    $solDist = new SolsticeDistribution($violencePerPerson, $violencePerterbation, $population);

    $violenceOnDay = [];

    $violenceJson = '{ "cols":[
        {"id":"","label":"Day","pattern":"","type":"number"},
        {"id":"","label":"Violent Incidents","pattern":"","type":"number"}
    ], "rows":[';

    $comma = ",";
    for($i = 1; $i <=365; $i++ ){
        $violenceOnDay[$i] = $solDist->valueForDay($i);
        $vio = $violenceOnDay[$i];
        $violenceJson = $violenceJson.'{"c":[{"v":'.$i.',"f":null},{"v":'.$vio.',"f":null}]}'.$comma."\n";
        if ($i == 364){
            $comma = "";
        }
    }

    $violenceJson = $violenceJson.']}';



    echo $violenceJson;


}
?>
