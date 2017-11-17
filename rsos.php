<div id="section">
    <h1>RSOs</h1>

    <?php
	session_start();
    include_once 'dbConnect.php';
    include_once 'functions.php';

    //If a user is logged in, display table info from database
    if ( (isset($_SESSION['loggedIn'])) && ($_SESSION['loggedIn'] == TRUE) ) :?>
        <p>Welcome <?php echo htmlentities($_SESSION['email']); ?>!</p>

        <?php

        if( isset($_GET['joinRSO']) ) {

            $joinRSO = $_GET['joinRSO'];
            echo "Join RSO ID: $joinRSO <br />";

            //Insert student ID and RSO ID into RSOMembership table
            $insert = "INSERT INTO rsomemberships (rID, sID) VALUES('".$joinRSO."', '".$_SESSION['sID']."')";
            if(mysqli_query($mysqli, $insert) === TRUE) { echo "Student ID and RSO ID inserted into RSOMembership successfully!<br />"; }
            else{ echo "<br />The following error occurred during insertion: " . $mysqli->error; }

            //Delete form variables after database insertion
            unset($_GET['joinRSO']);

            //return to homepage
            if (  (isset($_SESSION['adminType'])) && ($_SESSION['adminType'] == 1))
				header('Location: superAdministratorHomepage.php');
			else if ($_SESSION['adminType'] == 2)
				header('Location: administratorHomepage.php');
			else
				header('Location: studentHomepage.php');
        }

        if( isset($_GET['rsoName'], $_GET['rsoDescription']) ) {

            $rsoName = $_GET['rsoName'];
            $rsoDescription = $_GET['rsoDescription'];
            $universityID = $_GET['universityID'];

            echo "RSO Name: $rsoName <br />";
            echo "Description: $rsoDescription <br />";

            //Insert new rso into database
            $insert = "INSERT INTO rsos (name, description, belongsTo, studentAdmin, rsoManager)
              VALUES('" . $rsoName . "', '" . $rsoDescription . "', '" . $universityID . "', '" . $_SESSION['sID'] . "', '" . $_SESSION['sID'] . "')";

            //Check if insert was successful then display result
            if (mysqli_query($mysqli, $insert) === TRUE) {
                echo "RSO inserted into table successfully!<br /><br />";

                //Delete form variables after database insertion
                unset($_GET['rsoName']);
                unset($_GET['rsoDescription']);
                unset($_GET['joinRSO']);

                //Redirect to homepage
                if (  (isset($_SESSION['adminType'])) && ($_SESSION['adminType'] == 1))
					header('Location: superAdministratorHomepage.php');
				else if ($_SESSION['adminType'] == 2)
					header('Location: administratorHomepage.php');
				else
					header('Location: studentHomepage.php');
            }
            else {
                echo "<br />The following error occurred during insertion: " . $mysqli->error;
            }

        }//end if

        if (isset($_SESSION['adminType']))
            printTable($mysqli, "RSOsAndUniversity");
        else
            printTable2($mysqli, 'RSOsUniversityAndMembers', 'WHERE sID='.$_SESSION['sID'].
                ' AND rsoManager IS NOT NULL');

        //Close database connection
        $mysqli->close();
        ?>

    <?php else : ?>
        <!-- Default page content when user is not logged in -->

        <h2>Access Denied, you must login with admin privileges.</h2>

    <?php endif; ?>

</div>
