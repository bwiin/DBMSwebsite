<?php
	session_start();
    include_once 'dbConnect.php';
    include_once 'functions.php';
	?>

<!DOCTYPE html>
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
			Join RSO
		</div><!-- #header -->
		
		<!-- Modal Content -->
    <form class="modal-content animate" action="rsos.php" method="get">

            <?php rsoSelector($mysqli); ?>

            <center><button type="submit">Join RSO</button></center><br />
        
    </form>

		<div id="footer">
			<a href="redirect.php">Home</a> | COP4710 &#9733 Group 17 &#9733 Term Project
		</div><!-- #footer -->
		
	</div><!-- #wrapper -->
	
</body>

</html>