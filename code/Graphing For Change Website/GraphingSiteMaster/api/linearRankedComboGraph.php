<?php
/**
 * Created by PhpStorm.
 * User: bardo
 * Date: 2018-03-01
 * Time: 4:35 PM
 * */
//Including the db connection object
//You'll probably have to play around with this to get it to work
//with different operating systems and file/server structures
include_once $_SERVER["DOCUMENT_ROOT"].'/api/config/dbCreds.php';

//Creating a new database object and
//assigning the connection object to $db
$database = new Database();
$conn = $database->getConnection();

$scals = array(
    'accommodatesDisabilities' => .7,
    'qualifiesForRentalAssitance' => .6,
    'canYouthBeKeyTenant' => .4,
    'isFullyFurnished' => .4,
    'isKelownaResident' => .3,
    'isPetFriendly' => .2,
    'isSelfContained' => .6,
    'hasMaxDurationOfStay' => .6,
    'hasWaitList' => .1,
    'hasRTA' => .5,
    'onlyServesAboriginal' => .1,
    'unitsAreRGI' => .8,
    'unitsAreLowerEndOfMarket' => .2,
    'mustAbstainFromDrugsAndAlcohol' => .6,
    'male' => .5,
    'female' => .5,
    'hasMinimumIncomeRequirement' => .7,
    'hasMaximumIncomeRequirement' => .8,
    'requiresCleanTime' => .5,
    'requiresIncomeOrDisabilityAssitance' => .4,
    'requiresTreatmentFirst' => .4,
    'professionalSupportRequired' => .4,
    'targetsCouples' => .3,
    'targetsFamilies' => .5,
    'targetsIndividuals' => .3,
    'transgender' => .3,
    'caseManagement' => .4,
    'clothingOrHouseHoldGoods' => .4,
    'harmReduction' => .2,
    'healthOrDental' => .2,
    'hygiene' => .9,
    'laundry' => .4,
    'meals' => .7,
    'parking' => .1,
    'referrals' => .2,
    'showers' => .8,
    'storage' => .6
);

//Handle AJAX request and POST data here
//Check if there's available data in the POST variable
//under the legitimate key
//if(isset($_POST["req"]) && $_POST["req"] == 'diffs') {

    //Ensuring stmt var is null before preparing
    $stmt = null;

    //Preparing the statement based on req type
    //Check if 'teamInfo' was POSTed
        //If so, we send all info on the team members

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

            // retrieve our table contents
            // fetch() is faster than fetchAll()
            // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $score = 0;
                if(isset($row['orgID'])) {
                    foreach ($row as $key => $value) {
                        if (!isset($scals[$key]))
                            continue;

                        $score += $scals[$key] * $value;
                    }

                    $temp_loc_var = explode(", ", $row['geoLocation']);
                    $lon = floatval($temp_loc_var[0]);
                    $lat = floatval($temp_loc_var[1]);

                    $results_item = array(
                        "orgID" => $row['orgID'],
                        "score" => $score,
                        "lon" => $lon,
                        "lat" => $lat
                    );
                }
                array_push($results_arr, $results_item);
                //$results_arr[$row['orgID']] = $results_item;

                // run a second pass collect data and shit
            }
        }

        //Transform the results array into a JSON object
        //and send off to the front end
        echo json_encode($results_arr);

        //THIS IS CRUCIAL. PLEASE REMEMBER TO DO THIS.
        //Closing connection. Prepared statements (PDO)
        //only need to be nulled to be closed.
        $conn = null;
        $database = null;
    }
//}
?>