<?php
include_once 'globalConfig.php';

function printTable($mysqli, $tableName) {

    printTable2($mysqli, $tableName, null);
}

function printTable2($mysqli, $tableName, $whereClause) {

    //Query SQL Statement
    $query = "SELECT * FROM $tableName";

    //If all events by student, set the sID for the lookup.
    if ($tableName == 'AllEventsByStudent')
        $query = $query . ' WHERE sID=0 OR sID=' . $_SESSION['sID'] . ' ORDER BY name';

    //If attending events, set the sID for the lookup.
    if ($tableName == 'AttendEvents')
        $query = $query . ' WHERE sID=' . $_SESSION['sID'];

    //Up to coder at this point not to send a whereClause w/ AllEventsByStudent / AttendEvents
    if (isset($whereClause)) {

        $query = $query . ' ' . $whereClause;
    }

    $result = mysqli_query($mysqli, $query); //Run the query on the database
    $num_rows = mysqli_num_rows($result); //Find out how many rows are in the table

    if ($num_rows == 0) {

        echo '<p>No Items Found.</p>';
        return;
    }

    //Create HTML table
    echo '<table style="width:calc(100% - 5px)">';

    //Select correct table header based on table name
    switch ($tableName) {
        case 'addresses':
            echo '<tr><th>Address ID</th><th>Street 1</th><th>Street 2</th><th>City</th><th>State</th><th>Zip Code</th><th>Country</th></tr>';
            break;
        case 'comments':
            echo '<tr><th>Date & Time</th><th>Comment</th><th>Rating</th></tr>';
            break;
        case 'events':
        case 'AllEventsByStudent':
        case 'AttendEvents':
            echo '<tr><th>Event ID</th><th width="200px">Name</th><th>Description</th><th>Category</th><th width="80px">Start Date & Time</th><th width="80px">End Date & Time</th><th width="50px">Type</th></tr>';
            break;
        case 'locations':
            echo '<tr><th>Location ID</th><th>Name</th><th>Description</th><th>Latitude</th><th>Longitude</th></tr>';
            break;
        case 'rsos':
            echo '<tr><th>RSO ID</th><th>Name</th><th>Description</th></tr>';
            break;
        case 'RSOsAndUniversity':
        case 'RSOsUniversityAndMembers':
            echo '<tr><th>RSO ID</th><th>Name</th><th>Description</th><th>Associated University</th><th>Validated</th></tr>';
            break;
        case 'students':
            echo '<tr><th>Student ID</th><th>First Name</th><th>Last Name</th><th>Phone</th><th>E-Mail</th><th>Password</th></tr>';
            break;
        case 'universities':
            echo '<tr><th>University ID</th><th>Name</th><th>Description</th><th>Total Students</th><th>Email Suffix</th></tr>';
            break;
    }

    //Loop through each row of the select query and insert into the table
    for ($i = 0; $i < $num_rows; $i++) {

        //Get data for the current row
        $row = mysqli_fetch_assoc($result);

        //Select correct table format based on table name, then insert data from current row into table
        switch ($tableName) {
            case 'addresses':
                echo '<tr><td>' . $row["aID"] . '</td><td>' . $row["street1"] . '</td><td>' . $row["street2"] . '</td><td>' . $row["city"] .
                    '</td><td>' . $row["state"] . '</td><td>' . $row["zip"] . '</td><td>' . $row["country"] . '</td></tr>';
                break;
            case 'comments':
                echo '<tr><td>' . $row["dateTime"] . '</td><td>' . $row["comment"] . '</td><td>' . $row["rating"] . '</td></tr>';
                break;
            case 'events':
            case 'AllEventsByStudent':
            case 'AttendEvents':
                echo '<tr><td>' . $row["eID"] . '</td><td><a href="eventDetails.php?event=' . $row["eID"] . '">' . $row["name"] . '</a></td><td>' . $row["description"] . '</td><td>' . $row["category"] .
                    '</td><td>' . $row["startTime"] . '</td><td>' . $row["endTime"] . '</td><td>' . eventTypeToString($row["eventType"]) . '</td></tr>';
                break;
            case 'locations':
                echo '<tr><td>' . $row["locID"] . '</td><td>' . $row["name"] . '</td><td>' . $row["description"] . '</td><td>' . $row["latitude"] .
                    '</td><td>' . $row["longitude"] . '</td></tr>';
                break;
            case 'rsos':
                echo '<tr><td>' . $row["rID"] . '</td><td>' . $row["name"] . '</td><td>' . $row["description"] . '</td></tr>';
                break;
            case 'RSOsAndUniversity':
            case 'RSOsUniversityAndMembers':
                $verified = "Verified";
                if (!isset($row['rsoManager'])) {
                    $verified = "Unverified";
                }
                echo '<tr><td>' . $row["rID"] . '</td><td>' . $row["name"] . '</td><td>' . $row["description"] .
                    '</td><td>' . $row["universityName"] . '</td><td>' . $verified . '</td></tr>';
                break;
            case 'students':
                echo '<tr><td>' . $row["sID"] . '</td><td>' . $row["firstName"] . '</td><td>' . $row["lastName"] . '</td><td>' . $row["phone"] .
                    '</td><td>' . $row["email"] . '</td><td>' . $row["password"] . '</td></tr>';
                break;
            case 'universities':
                echo '<tr><td>' . $row["uID"] . '</td><td>' . $row["name"] . '</td><td>' . $row["description"] . '</td><td>' . $row["numStudents"] .
                    '</td><td>'.$row["emailSuffix"].'</td></tr>';
                break;
        }
    }

    echo '</table><br />'; //end table

}//end printTable

function getLocation(){

    //Create Google Maps script
    echo '<script>';
    
    //Declare map2 variable outside initialize function, so it can be resized later.
    echo 'var map2;';

    echo 'function initialize() {';

    //Set map properties
    echo 'var mapProp2 = { center:new google.maps.LatLng(28.602267,-81.200178), zoom:16, mapTypeId:google.maps.MapTypeId.ROADMAP };';
    echo 'map2=new google.maps.Map(document.getElementById("googleMapLoc"),mapProp2);';

    //Get latitude and longitude on mouse click and save values to input fields on events form
    //Input field identifiers: id="clickLatitude" id="clickLongitude"
    echo 'google.maps.event.addListener(map2, "click", function(event){ 
        document.getElementById(\'clickLatitude\').value = event.latLng.lat();
        document.getElementById(\'clickLongitude\').value = event.latLng.lng(); }); ';
    
    echo '}';

    //Draw map after window loads or changes size
    echo 'google.maps.event.addDomListener(window, \'load\', initialize); ';
    echo 'google.maps.event.addDomListener(window, \'resize\', initialize); ';
    
    echo '</script>';

    //Set map size
    echo '<div id="googleMapLoc" style="width:300px;height:300px;"></div>';

}//end getLocation

function mapEvents($mysqli, $sID){

    $query = "SELECT * FROM AttendEventsLocs WHERE sID=$sID";
    if ($_SESSION['view'] == 1) {

        //TODO: Process admin differently.
        $query = "SELECT * FROM AvailEventsLocs WHERE sID=0 OR sID=$sID";
        if (isset($_SESSION['adminType']))
            $query = "SELECT * FROM AllEventsLocs";
    }

    $result = mysqli_query($mysqli, $query); //Run the query on the database
    if($result){ $num_rows = mysqli_num_rows($result); } //Find out how many rows are in the table
    else{ $num_rows = 0; }

    //Create Google Maps script
    echo '<script>';

    echo 'function initialize() {';

    //Set map properties
    echo 'var mapProp = { center:new google.maps.LatLng(28.602267,-81.200178), zoom:16, mapTypeId:google.maps.MapTypeId.ROADMAP };';
    echo 'var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);';

    //Loop through each row of the select query and add event locations to the map
    for($i=0; $i < $num_rows; $i++) {

        //Get data for the current row
        $row = mysqli_fetch_assoc($result);

        //Place a color coded marker on the map for each event. public=green private=blue RSO=pink
        echo 'var marker'.$i.'=new google.maps.Marker({ position: {lat: '.$row["latitude"].', lng: '.$row["longitude"].'}, icon:\'img/marker'.$row["eventType"].'.png\', title:"'.$row["name"].'", map: map });';
    }
    
    echo '}';

    echo 'google.maps.event.addDomListener(window, \'load\', initialize);';

    echo '</script>';

    //Set map size
    echo '<div id="googleMap" style="width:600px;height:450px;"></div>';

}//end mapEvents

function eventTypeToString($type) {

    switch ($type) {
        case 0: return 'RSO';
        case 1: return 'Public';
        case 2: return 'Private';
    }

    return 'unknown';
}

function universitySelector($mysqli){

    //Query SQL Statement
    $query = "SELECT * FROM Universities"; //Select statement for all rows of the table
    $result = mysqli_query($mysqli, $query); //Run the query on the database
    $num_rows = mysqli_num_rows($result); //Find out how many rows are in the table
    
    //Add label and create university selector
    echo '<label><b>University</b></label><br />';
    echo '<select name="universityID" title="universityID">';

    //Loop through each row of the select query and add a select option
    for($i=0; $i < $num_rows; $i++) {

        //Get data for the current row
        $row = mysqli_fetch_assoc($result);
        
        //Add university name and uID to selector
        echo '<option value="'.$row["uID"].'">'.$row["name"].'</option>';
    }
    echo '</select><br />';
    
}//end universitySelector

function myUniversitySelector($mysqli) {

    $query = "SELECT * FROM Universities U INNER JOIN Enrollments E ON U.uID=E.uID ".
                "WHERE E.sID=".$_SESSION['sID']; //Select statement for all rows of the table

    $result = mysqli_query($mysqli, $query); //Run the query on the database
    $num_rows = mysqli_num_rows($result); //Find out how many rows are in the table

    //Add label and create university selector
    echo '<label><b>University</b></label><br />';
    echo '<select name="universityID" title="universityID">';

    //Loop through each row of the select query and add a select option
    for($i=0; $i < $num_rows; $i++) {

        //Get data for the current row
        $row = mysqli_fetch_assoc($result);

        //Add university name and uID to selector
        echo '<option value="'.$row["uID"].'">'.$row["name"].'</option>';
    }
    echo '</select><br />';
}

function rsoSelector($mysqli){
    
    //Had to use alias because of two rID columns
    //Select all RSOs belonging to student's university and not currently a member
    $lookup_sid = $_SESSION['sID'];
    $query = 'SELECT R.rID, R.name, R.description, R.belongsTo '.
	            'FROM RSOs R '.
                'WHERE rsoManager IS NOT NULL '.
                'AND belongsTo IN ('.
                    'SELECT E.uID FROM Enrollments E WHERE E.sID='.$lookup_sid.') '.
	            'AND rID NOT IN ('.
                    'SELECT M.rID FROM RSOMemberships M WHERE M.sID='.$lookup_sid.') '.
                'ORDER BY R.name';

    $result = mysqli_query($mysqli, $query); //Run the query on the database
    if($result){ $num_rows = mysqli_num_rows($result); } //Find out how many rows are in the table
    else{ $num_rows = 0;}

    //Add label and create RSO selector
    echo '<label><center><b>Select RSO to Join</b></center></label><br />';
    echo '<center><select name="joinRSO" title="joinRSO"></label>';

    //Loop through each row of the select query and add a select option
    for($i=0; $i < $num_rows; $i++) {

        //Get data for the current row
        $row = mysqli_fetch_assoc($result);

        //Add RSO name and rID to selector
        echo '<option value="'.$row["rID"].'">'.$row["name"].' - '.$row["description"].'</option>';
    }
    echo '</select><br />';

}//end rsoSelector

function getAddressInfo(){

    echo '<label><b>Street Address</b></label><br />';
    echo '<input type="text" placeholder="Enter Street Address" name="street1" required><br />';

    echo '<label><b>Apt/Suite/Etc</b></label><br />';
    echo '<input type="text" placeholder="Enter Street Address" name="street2"><br />';

    echo '<label><b>City</b></label><br />';
    echo '<input type="text" placeholder="Enter City" name="city" required><br />';

    echo '<label><b>State</b></label><br />';
    echo '<input type="text" placeholder="Enter State" name="state" required><br />';

    echo '<label><b>Zip Code</b></label><br />';
    echo '<input type="text" placeholder="Enter Zip Code" name="zip" required><br />';

    echo '<label><b>Country</b></label><br />';
    echo '<input type="text" placeholder="Enter Country" name="country" required><br />';
    
}//end getAddressInfo

function sqlDateToMDY($dt) {

    return date('M j, y - g:i a', strtotime($dt));
}

function eventDateTOsql($dt) {

    return date("Y-m-d H:i:s", strtotime($dt)); // 2001-03-10 17:16:18 (the MySQL DATETIME format)

}

function eventQRcode($mysqli, $eventID){

    //Get event details and location data
    $query = "SELECT E.name, E.startTime, E.endTime, L.name AS locName 
              FROM Events E INNER JOIN Locations L ON E.eID=L.locID 
              WHERE E.eID=".$eventID;

    $result = mysqli_query($mysqli, $query);
    $row = mysqli_fetch_assoc($result);
    
    $name = $row["name"];
    $location = $row["locName"];
    $startDateTime = $row["startTime"];
    $endDateTime = $row["endTime"];

    //Don't reformat this string (tabs, spaces, lf, etc) it will break the QR code
    $rawData = 'BEGIN:VEVENT
SUMMARY:'.$name.'
LOCATION:'.$location.'
DTSTART:'.date('Ymd\THis', strtotime($startDateTime)).'
DTEND:'.date('Ymd\THis', strtotime($endDateTime)).'
END:VEVENT';

    //Convert raw string data to URL format
    $eventData = urlencode($rawData);

    //Generate event QR code
    echo '<img style="margin:0px auto;display:block" src="http://api.qrserver.com/v1/create-qr-code/?data='.$eventData.'&size=175x175"/>';

}//end eventQRcode

function twitterFeed($twitterHandle){

    echo '<a class="twitter-timeline" data-lang="en" data-width="290" data-height="600" href="https://twitter.com/'.$twitterHandle.'">Tweets by '.$twitterHandle.'</a> <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>';

}