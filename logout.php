<h1>Logout</h1>

<?php
include_once 'dbConnect.php';

// Unset all session values
$_SESSION = array();

// Destroy session
session_destroy();

//Close database connection
$mysqli->close();

//Return to the home page
header('Location: login.php');

?>