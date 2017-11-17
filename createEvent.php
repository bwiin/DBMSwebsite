<?php
	session_start();
    include_once 'dbConnect.php';
    include_once 'functions.php';

    //If a user is logged in, display table info from database
    if ( (isset($_SESSION['loggedIn'])) && ($_SESSION['loggedIn'] == TRUE) )
	{
        if( isset($_GET['eventName'], $_GET['eventDescription'], $_GET['eventCategory'], $_GET['eventType'],
            $_GET['startDateTime'], $_GET['endDateTime'], $_GET['locationName'], $_GET['locationDescription'],
            $_GET['latitude'], $_GET['longitude']) ) 
		{

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
            if (mysqli_query($mysqli, $insert) === TRUE)
{
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

		//Close database connection
		$mysqli->close();
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />	
	
<body>

	<div id="wrapper">
		
		<div id="header">
			<eventHead>Create Event</eventHead>
		</div><!-- #header -->
		
			<!-- <div class="container"> -->
				<form method="get">
					<table>
						<table align="center">
						<col width="130">
						<col width="130">
						<tr>
							<th>Event name:</th>
							<th><input class="right" type="text" name="eventName" size=60 required></input></th>
						</tr>
						<tr>
							<td>Event type:</td>
							<td>
							<!--<input type="radio" name="eventType" value="0" checked=""> RSO
								<input type="radio" name="eventType" value="1"> Public
								<input type="radio" name="eventType" value="2"> Private	-->
								<select name="eventType" title="eventType">
									<option value="1">Public</option>
									<option value="2">Private</option>
									<option value="0">RSO</option>
								</select><br />
							</td>
						</tr>
						<tr>
							<td>Category:</td>
							<td>
								<select name="eventCategory" title="eventCategory">
									<option value=""></option>
									<option value="Arts & Entertainment">Arts & Entertainment</option>
									<option value="Athletics">Athletics</option>
									<option value="Career services">Career services</option>
									<option value="Convention">Convention</option>
									<option value="Community outreach">Community outreach</option>
									<option value="Fair/festivals">Fair/festivals</option>
									<option value="Fundraising">Fundraising</option>
									<option value="Games">Games</option>
									<option value="Health & recreation">Health & recreation</option>
									<option value="Intercultural">Intercultural</option>
									<option value="Meetings">Meetings</option>
									<option value="Military">Military</option>
									<option value="Music & concerts">Music & concerts</option>
									<option value="Presentations & lectures">Presentations & lectures</option>
									<option value="Religious & spiritual life">Religious & spiritual life</option>
									<option value="Social events">Social event</option>
									<option value="Sports">Sports</option>
									<option value="Tech Talk">Tech Talk</option>
									<option value="Theatre & visual arts">Theatre & visual arts</option>
									<option value="Training & workshops">Training & workshops</option>
									<option value="Volunteer">Volunteer</option>
								</select><br />
							</td>
						</tr>
						<tr>
							<td>Start Time:</td>
							<td><input class="right" type="datetime-local" name="startDateTime" required size=60></input></td>
						</tr>
						<tr>
							<td>End Time:</td>
								<td><input class="right" type="datetime-local" name="endDateTime" required size=60></input></td>
						</tr>
						<tr>
							<td>Description:</td>
							<td><textarea name="eventDescription" rows="4" cols="60"></textarea></td>
						</tr>
							<!--<tr>
								<td>Phone number:</td>
								<td><input class="right" type="text" name="usernameTXT" size=60></input></td>
							</tr>
							<tr>
								<td>Email:</td>
								<td><input class="right" type="text" name="usernameTXT" size=30></input>
									<input list="emailSuffixes" size=25>
									<datalist id="emailSuffixes">
										<option value="@famu.edu">
										<option value="@fau.edu">
										<option value="@fgcu.edu">
										<option value="@fiu.edu">
										<option value="@flpoly.edu">
										<option value="@fsu.edu">
										<option value="@ncf.edu">
										<option value="@Knights.ucf.edu">
										<option value="@ufl.edu">
										<option value="@unf.edu">
										<option value="@usf.edu">
										<option value="@uwf.edu">
									</datalist> 
							</tr> -->
						<tr>
							<td>Location name:</td>
							<td><input class="right" type="text" name="locationName" size=60 required></input></td>
						</tr>
						<tr>
							<td>Location Description</td>
							<td><input type="text" name="locationDescription" size=60></td>
						</tr>
						<tr>
							<td>Latitude:</td>
							<td><input class="right" type="text" name="latitude" size=60></input></td>
						</tr>
						<tr>
							<td>Longitude:</td>
							<td><input class="right" type="text" name="longitude" size=60></input></td>
						</tr>
						<tr>
								<th colspan="2"><input type="submit" value="Create event"></input><br /></th>
						</tr>
					</table>
				</form>
			<!-- </div> -->
		
	<div id="footer">
		<a href="redirect.php">Home</a> | COP4710 &#9733 Group 17 &#9733 Term Project
	</div><!-- #footer -->
		
</div><!-- #wrapper -->
	
</body>

</html>