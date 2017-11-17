<?php
	session_start();
	include_once 'dbConnect.php';
	
	if (  (isset($_SESSION['adminType'])) && ($_SESSION['adminType'] == 1))
		header('Location: superAdministratorHomepage.php');
	else if ($_SESSION['adminType'] == 2)
		header('Location: administratorHomepage.php');
	else
		header('Location: studentHomepage.php');
?>