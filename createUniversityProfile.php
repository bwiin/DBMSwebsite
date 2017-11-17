<?php
	session_start();
    include_once 'dbConnect.php';
    include_once 'functions.php';

	
    //If a user is logged in, display table info from database
    if ( (isset($_SESSION['loggedIn'])) && ($_SESSION['loggedIn'] == TRUE) )

        

        if( isset($_GET['universityName'], $_GET['universityDescription'], $_GET['numStudents'], $_GET['emailSuffix']) ) {

            $universityName = $_GET['universityName'];
            $universityDescription = $_GET['universityDescription'];
            $numStudents = $_GET['numStudents'];
			$emailSuffix = $_GET['emailSuffix'];

            //Insert new address into database
            /*$insert = "INSERT INTO addresses (street1 , street2, city, state, zip, country) ".
              "VALUES('".$street1."', '".$street2."', '".$city."', '".$state."', '".$zip."', '".$country."')";
            if(mysqli_query($mysqli, $insert) === TRUE) { echo "Address inserted into table successfully!<br />"; }
            else{ echo "<br />The following error occurred during insertion: " . $mysqli->error; }*/

            //Insert new event into database
            $insert = "INSERT INTO universities (name, description, numStudents, univManager, emailSuffix) ".
              "VALUES('" . $universityName . "', '" . $universityDescription . "', '" . $numStudents .
                    "', '" . $_SESSION['sID'] . "', '".$emailSuffix."')";

            //Check if insert was successful then display result
            if (mysqli_query($mysqli, $insert) === TRUE) {
                //echo "University inserted into table successfully!<br /><br />";

                //Delete form variables after database insertion
                unset($_GET['universityName']);
                unset($_GET['universityDescription']);
                unset($_GET['numStudents']);
                unset($_GET['emailSuffix']);

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

    //printTable($mysqli, 'universities');

    //Close database connection
    $mysqli->close();
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />	

	<!--[if lt IE 7]>
		<style type="text/css">
			#wrapper { height:100%; }
		</style>
	<![endif]-->
	
</head>

<body>

	<div id="wrapper">
		
		<div id="header">
			Create University Profile
		</div><!-- #header -->
		
		<div id="content">
			<article class="article">
				<div class="container">
					<form method="get">
						<table>
							<col width="130">
							<col width="130">
							<tr>
								<th>University name:</th>
								<td><input class="right" type="text" name="universityName" placeholder="Enter University Name" size=60></input></td>
								</td>
							</tr>
							<tr>
								<td>Email suffix:</td>
								<td><input class="right" type="text" name="emailSuffix" placeholder="example.edu" size=60></input></td>
							</tr>
							<tr>
								<td>Description:</td>
								<td><input class="right" type="text" name="universityDescription" placeholder="Enter a Description" size=60></input></td>
							</tr>
							<tr>
								<td>Picture:</td>
								<td><input class="right" type="text" placeholder="Enter URL/IMG" size=60></input></td>
							</tr>
							<tr>
								<td>Number of students:</td>
								<td><input class="right" type="text" name="numStudents" placeholder="Enter a number" size=60></input></td>
							</tr>
							<tr>
									<th colspan="2"><input type="submit" value="Create university profile"></input><br /></th>
							</tr>
						</table>
					</form>
				</div>
			</article>
		</div><!-- #content -->
		
		<div id="footer">
			<a href="redirect.php">Home</a> | COP4710 &#9733 Group 17 &#9733 Term Project
		</div><!-- #footer -->
		
	</div><!-- #wrapper -->
	
</body>

</html>