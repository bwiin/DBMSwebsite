<!-- Standard header for all pages -->
<?php require ('header.php'); ?>

<!-- Standard side menu for all pages -->
<?php require ('sideMenu.php'); ?>

<div id="section">
    <h1>Events</h1>

    <?php
    include_once 'dbConnect.php';
    include_once 'functions.php';

    //If a user is logged in, display table info from database
    if ( (isset($_SESSION['loggedIn'])) && ($_SESSION['loggedIn'] == TRUE) ) :?>
        <p>Welcome <?php echo htmlentities($_SESSION['email']); ?>!</p>

        <?php

        if( isset($_GET['eventName'], $_GET['eventDescription'], $_GET['eventCategory'], $_GET['eventType'],
            $_GET['startDateTime'], $_GET['endDateTime'], $_GET['locationName'], $_GET['locationDescription'],
            $_GET['latitude'], $_GET['longitude']) ) {

            $eventName = $_GET['eventName'];
            $eventDescription = $_GET['eventDescription'];
            $eventCategory = $_GET['eventCategory'];
            $eventType = $_GET['eventType'];
            $startDateTime = $_GET['startDateTime'];
            $endDateTime = $_GET['endDateTime'];
            $locationName = $_GET['locationName'];
            $locationDescription = $_GET['locationDescription'];
            $latitude = $_GET['latitude'];
            $longitude = $_GET['longitude'];

            echo "Event Name: $eventName <br />";
            echo "Description: $eventDescription <br />";
            echo "Category: $eventCategory <br />";
            echo "Type: $eventType <br />";
            echo "Start Date & Time: $startDateTime <br />";
            echo "End Date & Time: $endDateTime <br />";
            echo "Location Name: $locationName <br />";
            echo "Location Description: $locationDescription <br />";
            echo "Latitude: $latitude <br />";
            echo "Longitude: $longitude <br />";

            //Insert new location into database
            $insert = "INSERT INTO locations (name , description, latitude, longitude)
              VALUES('".$locationName."', '".$locationDescription."', '".$latitude."', '".$longitude."')";
            if(mysqli_query($mysqli, $insert) === TRUE) { echo "Location inserted into table successfully!<br />"; }
            else{ echo "<br />The following error occurred during insertion: ".$mysqli->error; }

            //Insert new event into database
            $insert = "INSERT INTO events (name, description, category, startTime, endTime, eventType, approved, manager, 
              locID, rsoEvent, privUniversity) VALUES('".$eventName."', '".$eventDescription."', '".$eventCategory."', 
              '".$startDateTime."', '".$endDateTime."', '".$eventType."', 0, '".$_SESSION['sID']."', LAST_INSERT_ID(), 1, 1)";

            //Check if insert was successful then display result
            if (mysqli_query($mysqli, $insert) === TRUE) {
                echo "Event inserted into table successfully!<br /><br />";

                //Delete form variables after database insertion
                unset($_GET['eventName']);
                unset($_GET['eventDescription']);
                unset($_GET['eventCategory']);
                unset($_GET['eventType']);
                unset($_GET['startDateTime']);
                unset($_GET['endDateTime']);
                unset($_GET['locationName']);
                unset($_GET['locationDescription']);
                unset($_GET['latitude']);
                unset($_GET['longitude']);

                //Reload page to delete variables in URL
                header('Location: events.php');
            }
            else {
                echo "<br />The following error occurred during insertion: " . $mysqli->error;

                // Show global arrays for troubleshooting
                echo '<br />Post: ';
                var_dump($_POST);
                echo '<br />Request: ';
                var_dump($_REQUEST);
                echo '<br />Get: ';
                var_dump($_GET);
            }

        }//end if

        if (isset($_SESSION['adminType']))
            printTable($mysqli, 'events');
        else
            printTable($mysqli, 'AllEventsByStudent');

        //Close database connection
        $mysqli->close();
        ?>

    <?php else : ?>
        <!-- Default page content when user is not logged in -->

        <h2>Access Denied, you must login with admin privileges.</h2>

    <?php endif; ?>

</div>

<!-- Student details for all pages -->
<?php require ('studentDetails.php'); ?>

<!-- Standard footer for all pages -->
<?php require ('footer.php'); ?>