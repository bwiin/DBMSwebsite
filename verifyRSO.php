<?php
	session_start();
    include_once 'dbConnect.php';
    include_once 'functions.php';

function endswith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}

function verifyRSO($mysqli, $students, $universityID) {

    if (count($students) < 5) {

        echo '<h2 style="color:red;">Minimum Number of Students Required</h2>';
        echo '<p>To request a new RSO, you must specify at least 5 people who interested in being in the group.</p>';
        return;
    }

    $invalid = array();
    $query = "SELECT * FROM Universities WHERE uID=".$universityID;
    $result = mysqli_query($mysqli, $query);
    $row = mysqli_fetch_assoc($result);
    $suffix = $row['emailSuffix'];
    for($i = 0; $i < count($students); $i++) {

        if (!endswith($students[$i], $suffix))
            $invalid[] = $students[$i];
    }

    if (count($invalid) != 0) {

        echo '<h2 style="color:red;">Invalid Email Address</h2>';
        echo '<p>The requested RSO is at the '.$row['name'].'.<br/>';
        echo 'All email addresses must end with: @'.$suffix.'</p>';
        echo '<p>Invalid email addresses:</p>';
        echo '<ul>';
        for($i = 0; $i < count($invalid); $i++) {

            echo '<li>'.$invalid[$i].'</li>';
        }
        echo '</ul>';
        return;
    }

    $query = "SELECT * FROM Students WHERE email='".$students[0]."'";
    $result = mysqli_query($mysqli, $query);
    $found = mysqli_num_rows($result);
    if($found == 0) {

        echo '<h2 style="color:red;">Invalid Email Address</h2>';
        echo '<p>The user requesting the RSO must already be registered with the website.</p>';
        return;
    }

    $row = mysqli_fetch_assoc($result);
    return $row['sID'];
}

?>

    <?php

    //If a user is logged in, display table info from database
    if (  (isset($_SESSION['loggedIn'])) && ($_SESSION['loggedIn'] == TRUE) ) :?>
        <?php

		
        $universityID = $_REQUEST['universityID'];

        $students = array();
        $studentsIn = $_REQUEST['student'];
        for($i = 0; $i < count($studentsIn); $i++) {

            $student = $studentsIn[$i];
            if (!empty($student)) {

                $students[] = $student;
            }
        }

        $studentAdmin = verifyRSO($mysqli, $students, $universityID);
        if (isset($studentAdmin)) {
            $query = "INSERT INTO RSOs (name, description, belongsTo, studentAdmin) ".
                        "VALUES ('".$_REQUEST['rsoName']."', '".$_REQUEST['rsoDescr']."', ".
                        $universityID.", ". $studentAdmin .")";
            $result = $mysqli->query($query);
            $newid = $mysqli->insert_id;

            for ($i=0; $i<count($students); $i++) {

                $query = "SELECT * FROM Students WHERE email='".$students[$i]."'";
                $result = mysqli_query($mysqli, $query);
                $found = mysqli_num_rows($result);
                if ($found == 0) {
                    echo '<p>The user email '.$students[$i].' was not found. Email sent requesting user join the website.</p>';
                }
                else {
                    $row = mysqli_fetch_assoc($result);
                    $sid = $row['sID'];
                    $query = "INSERT INTO RSOMemberships (rID, sID) values (".$newid.", ".$sid.")";
                    $result = mysqli_query($mysqli, $query);
                }
            }
			if($found != 0)
			{
				$query = "INSERT INTO admins (sID, adminType) values (".$_SESSION['sID'].", 2)";
				$result = mysqli_query($mysqli, $query);
				$_SESSION['adminType'] = 2;
				$mysqli->close();
				if (  (isset($_SESSION['adminType'])) && ($_SESSION['adminType'] == 1))
					header('Location: superAdministratorHomepage.php');
				else if ($_SESSION['adminType'] == 2)
					header('Location: administratorHomepage.php');
				else
					header('Location: studentHomepage.php');
				
			}
        }
			$mysqli->close(); //Close database connection
	?>

    <?php else : ?>
        <!-- Default page content when user is not logged in -->
        <!-- Put your code here to execute when the user is not logged in. -->
        <h2>Student is not logged in!</h2>
        <a href="login.php">Login</a>
    <?php endif; ?>
