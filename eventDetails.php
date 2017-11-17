<?php session_start(); ?>
<?php
$eID=$_REQUEST['event'];
$pgName = "eventDetails";
?>
<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/main.css" />
	<link rel="stylesheet" type="text/css" href="icomoon/style.css" />
	<!--breaks commenting and rating functionality probably needs certain css only
		to be edited inside of style.css rather than using their main.css-->

	<!--[if lt IE 7]>
		<style type="text/css">
			#wrapper { height:100%; }
		</style>
	<![endif]-->
	<!--Google map api here -->
	<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDk1OgO7rJc7ZaVp54pvzlkyl5jujF2wLE"></script>
</head>

<?php

include_once 'dbConnect.php';
include_once 'functions.php';

date_default_timezone_set('America/New_York');

$now = new DateTime();
$sID=$_SESSION['sID'];
$action="display";
if (isset($_REQUEST['action']))
    $action=$_REQUEST['action'];

switch($action) {
    case 'register':
        $query = "INSERT INTO AttendingEvents (sID, eID) VALUES (".$sID.", ".$eID.")";
        $result = mysqli_query($mysqli, $query);
        break;

    case 'unregister':
        $query = "DELETE FROM AttendingEvents WHERE sID=".$sID." AND eID=".$eID;
        $result = mysqli_query($mysqli, $query);
        break;

    case 'comment_add':
		// delete the comment
		$comment_dt = $_REQUEST['comment_dt'];
        $query = "DELETE FROM Comments WHERE eID=".$eID." AND sID=".$sID;
        $result = mysqli_query($mysqli, $query);
		// add new comment
        $comment_dt = $_REQUEST['comment_dt'];
        $comment=$_REQUEST['comment'];
        $rating=$_REQUEST['rating'];
		$stmt = $mysqli->prepare("INSERT INTO Comments (eID, sID, dateTime, comment, rating) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param("iissi", $eID, $sID, $comment_dt, $comment, $rating);
        $stmt->execute();
        $stmt->close();
        break;

    case 'comment_delete':
        $comment_dt = $_REQUEST['comment_dt'];
        $query = "DELETE FROM Comments WHERE eID=".$eID." AND sID=".$sID." AND dateTime='".$comment_dt."'";
        $result = mysqli_query($mysqli, $query);
        break;

    case 'comment_edit':
		// delete the comment
		$comment_dt = $_REQUEST['comment_dt'];
        $query = "DELETE FROM Comments WHERE eID=".$eID." AND sID=".$sID;
        $result = mysqli_query($mysqli, $query);
		// add new comment
        $comment_dt = $_REQUEST['comment_dt'];
        $comment=$_REQUEST['comment'];
        $rating=$_REQUEST['rating'];
		$stmt = $mysqli->prepare("INSERT INTO Comments (eID, sID, dateTime, comment, rating) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param("iissi", $eID, $sID, $comment_dt, $comment, $rating);
        $stmt->execute();
        $stmt->close();
        break;
}

$query = "SELECT COUNT(sID) AS NumAttending FROM AttendingEvents WHERE eID=".$eID;
$result = mysqli_query($mysqli, $query);
$row = mysqli_fetch_assoc($result);
$numAttending = $row["NumAttending"];

// Select the event from the database.
$query = "SELECT * FROM EventsWithLocations WHERE eID = ".$eID;
$result = mysqli_query($mysqli, $query);
$num_rows = mysqli_num_rows($result);
$erow = mysqli_fetch_assoc($result);
$eventType = $erow["eventType"];

// Determine if the student is attending this event.
$query = "SELECT * FROM AttendEvents WHERE sID=".$sID." AND eID=".$eID;
$result = mysqli_query($mysqli, $query);
$attending = mysqli_num_rows($result);

if ($eventType == 0) {
    $query = "SELECT * FROM RSOs WHERE rID=".$erow["rsoEvent"];
    $result = mysqli_query($mysqli, $query);
    $rrow = mysqli_fetch_assoc($result);
    $rsoEvent = $rrow["name"]." - ".$rrow["description"];

    $query = "SELECT * FROM Universities WHERE uID=".$rrow["belongsTo"];
    $result = mysqli_query($mysqli, $query);
    $urow = mysqli_fetch_assoc($result);
    $privUniversity = $urow["name"];
}
else if ($eventType == 2) {
    $query = "SELECT * FROM Universities WHERE uID=".$erow["privUniversity"];
    $result = mysqli_query($mysqli, $query);
    $urow = mysqli_fetch_assoc($result);
    $privUniversity = $urow["name"];
}
?>

<script>
    function setEditRating(rating) {

        var sel = document.getElementById('edit_rating');
        sel.selectedIndex = rating;
    }

    function setEditComment(commentID) {

        var src = document.getElementById('hidden_comment_' + commentID);
        var dst = document.getElementById('edit_comment');
        dst.value=src.value;
    }

    function setEditCommentDT(dateTime) {

        var hdn = document.getElementById('edit_comment_dt');
        hdn.value=dateTime
    }

    function initializeMap() {
        var eMapProps = {
            center: new google.maps.LatLng(
                <?php echo $erow["latitude"] . "," . $erow["longitude"] ?>),
            zoom: 16,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var eMap = new google.maps.Map(
            document.getElementById("edGoogleMap"), eMapProps);

        var eMarker = new google.maps.Marker({
            position: {
                lat: <?php echo $erow["latitude"] ?>,
                lng: <?php echo $erow["longitude"] ?>},
            icon: "img/marker<?php echo $eventType.'.png'?>",
            title: "<?php echo $erow["name"]?>",
            map: eMap
        });
    }
    google.maps.event.addDomListener(window, 'load', initializeMap);
</script>
<div id="section">
    <h1>Event: <?php echo $erow["name"]; ?></h1>
    <h2><?php echo $erow["description"]; ?></h2>
    <?php
    $date = new DateTime($erow["endTime"]);

    if ($attending > 0) {
        if ($date >= $now) {
            echo '<h3>You are attending this event.</h3>';
        } else {
            echo '<h3>You attended this event.</h3>';
        }
    }
    ?>
    <div class="edTableDiv">
    <table class="edTable">
        <tr>
            <td class="edTableLeft">Event Name</td>
            <td class="edTableRight"><?php echo $erow["name"]; ?></td>
        </tr>
        <tr>
            <td class="edTableLeft">Description</td>
            <td class="edTableRight"><?php echo $erow["description"]; ?></td>
        </tr>
        <tr>
            <td class="edTableLeft">Category</td>
            <td class="edTableRight"><?php echo $erow["category"]; ?></td>
        </tr>
        <tr>
            <td class="edTableLeft">Starts</td>
            <td class="edTableRight"><?php echo sqlDateToMDY($erow["startTime"]); ?></td>
        </tr>
        <tr>
            <td class="edTableLeft">Ends</td>
            <td class="edTableRight"><?php echo sqlDateToMDY($erow["endTime"]); ?></td>
        </tr>
        <tr>
            <td class="edTableLeft">Type</td>
            <td class="edTableRight">
                <?php
                    echo eventTypeToString($eventType);
                if (isset($privUniversity)) {
                    echo '<br/>Hosted by: '.$privUniversity;
                }
                if (isset($rsoEvent)) {
                    echo '<br/>Organization Name: '.$rsoEvent;
                }
                ?>
            </td>
        </tr>
        <tr>
            <td class="edTableLeft">Number Attendees</td>
            <td class="edTableRight"><?php echo $numAttending ?></td>
        </tr>
    </table>
    <br/>
    <?php
    if ($attending > 0) {
        if($date >= $now) {
            echo '<form action="eventDetails.php" method="get" name="form_unreg" id="form_unreg">';
            echo '<input type="hidden" name="action" value="unregister"/>';
            echo '<input type="hidden" name="event" value="'.$eID.'"/>';
            echo '<a href="eventDetails.php" class="edButton" onclick="document.getElementById(\'form_unreg\').submit(); return false">';
            echo 'Unregister from this Event</a>&nbsp;';
            echo '</form>';
        }
        else {
            ?>
            <a class="edButton" onclick="document.getElementById('ed01').style.display='block'">Comment on this Event</a>
            <?php
        }
    }
    else if ($date >= $now) {
        echo '<form action="eventDetails.php" method="get" name="form_reg" id="form_reg">';
        echo '<input type="hidden" name="action" value="register"/>';
        echo '<input type="hidden" name="event" value="'.$eID.'"/>';
        echo '<a href="eventDetails.php" class="edButton" onclick="document.getElementById(\'form_reg\').submit(); return false;">';
        echo 'Register for this Event</a>&nbsp;';
        echo '</form>';
    }
    ?>

    </div>
    <div id="edGoogleMap"></div>

	<!-- social media integration -->
	<div style="padding-bottom: 40px;" class="fb-save"
	data-uri="https://www.facebook.com"
	data-size="small">
	</div>
	
    <?php
        $query = "SELECT *, C.sID as C_sID FROM Comments C INNER JOIN Students S ON C.sID = S.sID WHERE C.eID=".$eID." ORDER BY dateTime";
        $result = mysqli_query($mysqli, $query);
        $num_rows = mysqli_num_rows($result); //Find out how many rows are in the table
        if ($num_rows > 0) {
        ?>
        <div>
            <h2>Comments:</h2>
            <table class="edCommentTable">
                <tr>
                    <th width="160px">Date</th>
                    <th width="100px">Rating</th>
                    <th>Commenter / Comment</th>
                </tr>
                <?php
                for ($i = 0; $i < $num_rows; $i++) {

                    $row = mysqli_fetch_assoc($result);
                    $rating = "";
                    for ($j = 0; $j < $row["rating"]; $j++)
                        $rating = $rating . '<span class="icon-star-full"></span>';

                    echo '<tr><td>'.sqlDateToMDY($row["dateTime"]).'</td>';
                    echo '<td>'.$rating.'</td>';
                    echo '<td><span style="font-style: italic; float:left;">'.$row["firstName"].' '.$row["lastName"].'</span>';
                    if ($row["C_sID"] == $sID || isset($_SESSION['adminType'])) {
                        echo '<a style="float:right;" class="edButton" onclick="setEditRating('.$row["rating"].'); setEditComment('.$i.'); setEditCommentDT(\''.$row["dateTime"].'\'); document.getElementById(\'ed02\').style.display=\'block\'"><span class="icon-pencil"></span></a>';
                        echo '</form>';
                        echo '<form style="float:right;" action="eventDetails.php" method="get" name="form_delcomment_'.$i.'" id="form_delcomment_'.$i.'">';
                        echo '<input type="hidden" name="action" value="comment_delete"/>';
                        echo '<input type="hidden" name="event" value="'.$eID.'"/>';
                        echo '<input type="hidden" name="comment_dt" value="'.$row['dateTime'].'"/>';
                        echo '<textarea style="display:none;" name="hidden_comment_'.$i.'" id="hidden_comment_'.$i.'">'.$row['comment'].'</textarea>';
                        echo '<a href="eventDetails.php" class="edButton" onclick="document.getElementById(\'form_delcomment_'.$i.'\').submit(); return false">';
                        echo '<span class="icon-bin"></span></a>&nbsp;';
                        echo '</form>';
                    }
                    echo '<br/><span style="float:left;">'.nl2br($row["comment"]).'</span></td></tr>';
                }
                ?>
            </table>
        </div>
        <?php
        }

        if($numAttending > 0) {
            ?>
            <div style="padding-top: 35px;">
                <h2>Attendees:</h2>
                <table class="edAttendeesTable">
                    <tr>
                        <th width="25%">Name</th>
                        <th width="75%">Attending University</th>
                    </tr>
                    <?php
                    $query = "SELECT * FROM AttendingEvents AE INNER JOIN Students S ON AE.sID = S.sID WHERE AE.eID=".$eID." ORDER BY lastName, firstName";
                    $result = mysqli_query($mysqli, $query);
                    $num_rows = mysqli_num_rows($result); //Find out how many rows are in the table
                    for ($i = 0; $i < $num_rows; $i++) {

                        $row = mysqli_fetch_assoc($result);

                        $query2 = "SELECT * FROM Enrollments E INNER JOIN Universities U ON E.uID=U.uID WHERE E.sID=".$row["sID"]." ORDER BY name";
                        $result2 = mysqli_query($mysqli, $query2);
                        $num_rows2 = mysqli_num_rows($result2);
                        $univ = "";
                        for ($j = 0; $j < $num_rows2; $j++) {

                            $row2 = mysqli_fetch_assoc($result2);
                            $univ = $univ . ", " . $row2["name"];
                        }
                        $univ = substr($univ, 2);
                        echo '<tr><td>'.$row["firstName"].' '.$row["lastName"].'</td>';
                        echo '<td>' . $univ . '</td></tr>';
                    }
                    ?>
                </table>
            </div>
            <?php
        }
    ?>
</div>
<!-- Display Add comment button -->
				<button class="commentbtn" onclick="document.getElementById('ed01').style.display='block'">Add Comment</button><br />
<!-- Add Comment -->
<div id="ed01" class="modal">
  <span onclick="document.getElementById('ed01').style.display='none'"
        class="close" title="Close Modal">&times;</span>

    <!-- Modal Content -->
    <form class="modal-content animate" action="eventDetails.php" method="get" name="ed01_form">

        <div class="container">
            <h2>Add a Comment</h2>
            <table class="edCommentEntry">
                <tr>
                    <td width="60px"">Rating:</td>
                    <td>
                        <select name="rating" title="rating">
                            <option value="0">&lt;No Stars&gt;</option>
                            <option value="1">&#x2605;</option>
                            <option value="2">&#x2605;&#x2605;</option>
                            <option value="3">&#x2605;&#x2605;&#x2605;</option>
                            <option value="4">&#x2605;&#x2605;&#x2605;&#x2605;</option>
                            <option value="5">&#x2605;&#x2605;&#x2605;&#x2605;&#x2605;</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Comment:</td>
                    <td><pre><textarea style="width: 98%;" rows="5" name="comment" title="comment"></textarea></pre></td>
                </tr>
            </table>

            <input type="hidden" name="event" value="<?php echo $eID ?>"/>
            <input type="hidden" name="action" value="comment_add"/>
            <input type="hidden" name="comment_dt" value="<?php echo $now->format('Y-m-d H:i:s') ?>"/>
            <button type="submit" onclick="document.getElementById('ed01').style.display='none'">Add Comment</button><br />
        </div>

        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="document.getElementById('ed01').style.display='none'" class="cancelbtn">Cancel</button>
            <span class="psw"></span>
        </div>
    </form>
</div>
<!-- Display edit comment button -->
				<button class="editbtn" onclick="document.getElementById('ed02').style.display='block'">Edit your Comment</button><br />
<!-- Edit Comment -->
<div id="ed02" class="modal">
  <span onclick="document.getElementById('ed02').style.display='none'"
        class="close" title="Close Modal">&times;</span>

    <!-- Modal Content -->
    <form class="modal-content animate" action="eventDetails.php" method="get" name="ed02_form">

        <div class="container">

            <h2>Edit My Comment</h2>
            <table class="edCommentEntry">
                <tr>
                    <td width="60px"">Rating:</td>
                    <td>
                        <select name="rating" title="rating" id="edit_rating">
                            <option value="0">&lt;No Stars&gt;</option>
                            <option value="1">&#x2605;</option>
                            <option value="2">&#x2605;&#x2605;</option>
                            <option value="3">&#x2605;&#x2605;&#x2605;</option>
                            <option value="4">&#x2605;&#x2605;&#x2605;&#x2605;</option>
                            <option value="5">&#x2605;&#x2605;&#x2605;&#x2605;&#x2605;</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Comment:</td>
                    <td><pre><textarea id="edit_comment" style="width: 98%;" rows="5" name="comment" title="comment"></textarea></pre></td>
                </tr>
            </table>

            <input type="hidden" name="event" value="<?php echo $eID ?>"/>
            <input type="hidden" name="action" value="comment_edit"/>
            <input type="hidden" id="edit_comment_dt" name="comment_dt" value=""/>
            <button type="submit" onclick="document.getElementById('ed02').style.display='none'">Update Comment</button><br />
        </div>

        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="document.getElementById('ed02').style.display='none'" class="cancelbtn">Cancel</button>
            <span class="psw"></span>
        </div>
    </form>
</div>
<div id="footer">
			<a href="redirect.php">Home</a> | COP4710 &#9733 Group 17 &#9733 Term Project
		</div><!-- #footer -->
<?php
$mysqli->close();
?>