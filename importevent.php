<div id="section">

    <?php
	session_start();
    include_once 'dbConnect.php';
    include_once 'functions.php';

    //If a user is logged in, display table info from database
    if (  (isset($_SESSION['loggedIn'])) && ($_SESSION['loggedIn'] == TRUE) ) :?>
        <?php

        $query = "SELECT * from Events WHERE externID=".$_REQUEST['evt_eventid'];
        $result = mysqli_query($mysqli, $query);
        $found = mysqli_num_rows($result);
        if ($found == 0) {

            $name = $_REQUEST['evt_name'];
            $descr = $_REQUEST['evt_descr'];
            $category = $_REQUEST['evt_category'];
            $startTime = eventDateTOsql($_REQUEST['evt_start']);
            $endTime = eventDateTOsql($_REQUEST['evt_end']);
            $externID = $_REQUEST['evt_eventid'];
            $externSrc = $_REQUEST['evt_eventsrc'];

            if ($stmt = $mysqli->prepare(
                "INSERT INTO Events (name, description, category, startTime, endTime, ".
                "eventType, approved, manager, locID, privUniversity, ".
                "externID, externSource) ".
                "VALUES (?, ?, ?, ?, ?, ".
                "2, 1, 1, 1, 1, ".
                "?, ?)")) {

                $stmt->bind_param("sssssis",
                    $name,
                    $descr,
                    $category,
                    $startTime,
                    $endTime,
                    $externID,
                    $externSrc);

                if ($insertResult = $stmt->execute()) {

                    $new_eID = $mysqli->insert_id;
                    $stmt->close();

                    //Close database connection
                    $mysqli->close();
					// refresh the page, grab adminType to check what user
					if (  (isset($_SESSION['adminType'])) && ($_SESSION['adminType'] == 1))
						header('Location: superAdministratorHomepage.php');
					else if ($_SESSION['adminType'] == 2)
						header('Location: administratorHomepage.php');
					else
						header('Location: studentHomepage.php');
                }
                else {
                    
                    echo '<h2>Failed to Import Event</h2>';
                    echo '<h3>' . $mysqli->error . '</h3>';
                }
            }
            else {

                echo '<h2>Failed to Import Event</h2>';
                echo '<h3>' . $mysqli->error . '</h3>';
            }
        }
        else {

            echo '<h2>Failed to Import Event</h2>';
            echo '<h3>Event Already Exists</h3>';
        }
        ?>

    <?php else : ?>
        <!-- Default page content when user is not logged in -->
        <!-- Put your code here to execute when the user is not logged in. -->
        <h2>Student is not logged in!</h2>

    <?php endif; ?>

</div>

<!-- Student details for all pages -->
<!-- <?php require ('studentDetails.php'); ?> --!>