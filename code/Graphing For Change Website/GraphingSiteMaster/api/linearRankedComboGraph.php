<?php
include_once $_SERVER["DOCUMENT_ROOT"].'/api/config/dbCreds.php';

//Creating a new database object and
//assigning the connection object to $db
$database = new Database();
$conn = $database->getConnection();

$scals = array(
    'showers' => .9,
    'meals' => 1,
    'hygiene' => 1,
    'laundry' => .5,
    'clothingOrHouseHoldGoods' => .4,
    'storage' => .3
);

    //Ensuring stmt var is null before preparing
    $stmt = null;

    $sql = "SELECT * FROM Accommodation AS a INNER JOIN Demographic AS d ON d.orgID=a.orgID INNER JOIN Service AS s ON s.orgID=d.orgID INNER JOIN Organization AS o ON o.orgID=a.orgID";
    $stmt = $conn->prepare($sql);


    //Checking to see if we prepared a statement
    if ($stmt != null) {
        // execute query
        $stmt->execute();

        //Count rows
        $num = $stmt->rowCount();

        // check if more than 0 record found
        if ($num > 0){
            // products array
            $results_arr = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $score = 0;
                if(isset($row['orgID'])) {
                    foreach ($row as $key => $value) {
                        if (!isset($scals[$key]))
                            continue;

                        $score += $scals[$key] * $value;
                    }

                    $temp_loc_var = explode(", ", $row['geoLocation']);
                    $lat = floatval($temp_loc_var[0]);
                    $lon = floatval($temp_loc_var[1]);

                    $results_item = array(
                        "orgID" => $row['orgID'],
                        "score" => $score,
                        "lon" => $lon,
                        "lat" => $lat
                    );
                }
                array_push($results_arr, $results_item);
                //$results_arr[$row['orgID']] = $results_item;
            }
        }

        //Transform the results array into a JSON object
        //and send off to the front end
        echo json_encode($results_arr);

        $conn = null;
        $database = null;
    }
//}
?>