<?php
include_once 'globalConfig.php';

//Open connection to the database
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE, PORT);
if($mysqli->connect_errno){ echo "Failed to connect to MySQL: (".$mysqli->connect_errno.") ".$mysqli->connect_error; }

//Select database to use
$mysqli->select_db(DATABASE);