<?php session_start(); ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/main.css" />	

	<!--[if lt IE 7]>
		<style type="text/css">
			#wrapper { height:100%; }
		</style>
	<![endif]-->
	
</head>

<body>

	<div id="wrapper">
		
		<div id="header">
			Administrator Homepage
		</div><!-- #header -->
		
		<div id="content">
			<div class="container">
				<nav>
					<ul>
						<li>
							<div class="vertical-menu">
								<a href="administratorHomepage.php">Home</a>
								<a href="createEvent.php">Create Event</a>
								<a href="createRSO.php">Create RSO</a>
								<a href="joinRSO.php">Join RSO</a>
								<a href="logout.php">Logout</a>
							</div></li>
					</ul>
				</nav>
			</div>
		</div><!-- #content -->
		<div id="section">

			<?php
			include_once 'dbConnect.php';
			include_once 'functions.php';

			//If a user is logged in, display events table info from database
			if ( (isset($_SESSION['loggedIn'])) && ($_SESSION['loggedIn'] == TRUE) ) {

				//Show all available events that the student can see, or all for admin.
				if (!isset($_SESSION['view'])) {

					$_SESSION['view'] = 1;
				}

				if ($_SESSION['view'] == 1) { ?>

					<h1>Welcome <?php echo htmlentities($_SESSION['firstName']); ?></h1>
					<div style="float: left; clear: none; width: calc(100% - 5px); height: 75px;">
						<div style="float: left;"><h2>All Available Events</h2></div>
						<div style="float: right; line-height: 75px;">
							<a href="administratorHomepage.php" onclick="location.href=this.href+'?view=2'; return false;">View My Events</a>
						</div>
					</div>

					<?php

					//Print table of all events
					if (!isset($_SESSION['adminType'])) {

						printTable($mysqli, 'AllEventsByStudent');
					} else {

						printTable($mysqli, 'events');
					}
					?>
					<!-- <h2>Event Locations:</h2> 
					<?php
					//Draw map and overlay markers for student's events
					mapEvents($mysqli, $_SESSION['sID']);   
					?>
					does not currently work -->
					<div id="import_div" style="width:100%;padding-bottom:50px;">
						<br/>
						<h2>Events from UCF Website:</h2>
						<p>As a registered student of UCF, you can import events from the school's main site.</p>
						<script>
							function displayEventsToImport(blob) {
								window.data = blob;
								var sel = document.getElementById("d_select");
								for(var i = 0; i < blob.length; i++) {

									var opt = document.createElement("option");
									opt.innerHTML = blob[i].title;
									opt.value = i;
									sel.appendChild(opt);
								}
							}

							function changeEvent() {

								var sel = document.getElementById("d_select");
								var item = window.data[sel.selectedIndex];

								var cell = document.getElementById("d_title");
								var input = document.getElementById("evt_name");
								cell.innerHTML = item.title;
								input.value = item.title;

								cell = document.getElementById("d_subtitle");
								cell.innerHTML = item.subtitle;

								cell = document.getElementById("d_descr");
								input = document.getElementById("evt_descr");
								cell.innerHTML = item.description;
								input.value = item.description;

								cell = document.getElementById("d_cat");
								input = document.getElementById("evt_category");
								cell.innerHTML = item.category;
								input.value = item.category;

								cell = document.getElementById("d_when");
								cell.innerHTML = item.starts + " - " + item.ends;
								cell = document.getElementById("d_loc");
								input = document.getElementById("evt_start");
								input.value = item.starts;
								input = document.getElementById("evt_end");
								input.value = item.ends;

								while (cell.firstChild) {
									cell.removeChild(cell.firstChild);
								}
								
								var link = document.createElement("a");
								link.href = item.location_url;
								link.innerHTML = item.location;
								link.target = "_blank";
								cell.appendChild(link);

								input = document.getElementById("evt_eventid");
								input.value = item.eventinstance_id;
							}

							var xhttp = new XMLHttpRequest();
							xhttp.onreadystatechange = function() {
								if (xhttp.readyState == XMLHttpRequest.DONE ) {
									if (xhttp.status == 200) {
										var str = xhttp.responseText;
										var json = JSON.parse(str);

										displayEventsToImport(json);
										changeEvent();
									}
									else if (xhttp.status == 400) {
										alert('There was an error 400');
									}
									else {
										alert('something else other than 200 was returned');
									}
								}
							};
							xhttp.open("GET", "http://events.ucf.edu/this-week/feed.json", true);
							xhttp.send();
						</script>
						<form action="importevent.php" method="get" name="form_import" id="form_import">
							<select title="d_select" id="d_select" onchange="changeEvent();"></select>
							<input type="hidden" name="evt_name" id="evt_name" value="value"/>
							<input type="hidden" name="evt_descr" id="evt_descr" value="value"/>
							<input type="hidden" name="evt_category" id="evt_category" value="value"/>
							<input type="hidden" name="evt_start" id="evt_start" value="value"/>
							<input type="hidden" name="evt_end" id="evt_end" value="value"/>
							<input type="hidden" name="evt_eventid" id="evt_eventid" value="value"/>
							<input type="hidden" name="evt_eventsrc" id="evt_eventsrc" value="events.ucf.edu"/>
							<table width="100%" class="idx">
								<tr>
									<td class="idxleft" width="20%">Title</td>
									<td class="idxright" id="d_title"></td>
								</tr>
								<tr>
									<td class="idxleft">Sub Title</td>
									<td class="idxright" id="d_subtitle"></td>
								</tr>
								<tr>
									<td class="idxleft">Description</td>
									<td class="idxright" id="d_descr"></td>
								</tr>
								<tr>
									<td class="idxleft">Category</td>
									<td class="idxright" id="d_cat"></td>
								</tr>
								<tr>
									<td class="idxleft">When</td>
									<td class="idxright" id="d_when"></td>
								</tr>
								<tr>
									<td class="idxleft">Location</td>
									<td class="idxright" id="d_loc"></td>
								</tr>
							</table>
							<br/>
							<a href="importevent.php" class="edButton"
							   onclick="document.getElementById('form_import').submit(); return false">Add this Event</a>
						</form>
					</div>
					<?php
				}
				//Show just the events that the user (admin or student) has registered for.
				else { ?>

					<h1>Welcome <?php echo htmlentities($_SESSION['firstName']); ?>!</h1>
					<div style="float: left; clear: none; width: calc(100% - 50px); height: 75px;">
						<div style="float: left;"><h2>Your Events</h2></div>
						<div style="float: right; line-height: 75px;">
							<a href="index.php" onclick="location.href=this.href+'?view=1'; return false;">View All Events</a>
						</div>
					</div>
					<?php printTable($mysqli, 'AttendEvents'); ?>
					<?php mapEvents($mysqli, $_SESSION['sID']); ?>
				<?php
				}
			}
			else { ?>
				<!-- Default page content when user is not logged in -->

				<!-- Call to action message -->
				

				<!-- Events Collage -->
				

			<?php
			} ?>
		</div>
		<div id="footer">
			<a href="administratorHomepage.php">Home</a> | COP4710 &#9733 Group 17 &#9733 Term Project
		</div><!-- #footer -->
		
	</div><!-- #wrapper -->
	
</body>

</html>