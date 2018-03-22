<?php
//Including the db connection object
//You'll probably have to play around with this to get it to work
//with different operating systems and file/server structures
include_once $_SERVER["DOCUMENT_ROOT"].'/api/config/dbCreds.php';

//Creating a new database object and
//assigning the connection object to $db
$database = new Database();
$conn = $database->getConnection();

//Handle AJAX request and POST data here
//Check if there's available data in the POST variable
//under the legitimate key
if(isset($_POST["req"])) {

    //Ensuring stmt var is null before preparing
    $stmt = null;

    //Preparing the statement based on req type
    //Check if 'teamInfo' was POSTed
    if(strcmp($_POST["req"], "all") == 0) {
        //If so, we send all info on the team member
        $sql = "select orgID, geoLocation from Organization";
        $stmt = $conn->prepare($sql);

    } else if (strcmp($_POST["req"], "female") == 0) {
        //Also check if power leves were requested, if so
        //we serve up the power levels and associated names.
        $sql = "select orgID, geoLocation from Organization where orgID in (Select orgId from Demographic where female = true AND male = false AND transgender = false)";
        $stmt = $conn->prepare($sql);
    } else if (strcmp($_POST["req"], "transgender") == 0) {
        //Also check if power leves were requested, if so
        //we serve up the power levels and associated names.
        $sql = "select orgID, geoLocation from Organization where orgID in (Select orgId from Demographic where transgender = true)";
        $stmt = $conn->prepare($sql);
    }

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
                // extract row
                // this will make $row['name'] to
                // just $name only
                extract($row);

                $temp_loc_var = explode(", ", $geoLocation);
                $lon = floatval($temp_loc_var[0]);
                $lat = floatval($temp_loc_var[1]);

                $results_item=array(
                    "lon" => $lon,
                    "lat" => $lat
                );


                //Pushing associated array into $results_array
                array_push($results_arr, $results_item);
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
}
?>