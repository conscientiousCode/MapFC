
<?php

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
?>
