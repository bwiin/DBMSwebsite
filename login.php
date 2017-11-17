<?php
		include_once 'dbConnect.php';
		include_once 'functions.php';

		session_start();
		if(isset($_GET['email'], $_GET['password'])){

		//Create local variables
		$email = $_GET['email'];
		$password = $_GET['password'];

		//Check if email/password are in the database
		if (!($stmt = $mysqli->prepare(
            "SELECT S.firstName, S.lastName, S.email, S.sID, A.adminType, E.uID, S.password ".
            "FROM Students S ".
            "LEFT OUTER JOIN Admins A ON A.sID = S.sID ".
            "LEFT OUTER JOIN Enrollments E ON E.sID = S.sID ".
             "WHERE S.email=?"))) {

        echo '<p>Failed to prepare login statement: '. $mysqli->errno . ' - ' . $mysqli->error;
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {

        echo '<p>Failed to execute login statement: '. $mysqli->errno . ' - ' . $mysqli->error;
    }

    if (!($result = $stmt->get_result())) {

        echo '<p>Failed to get a result: '. $mysqli->errno . ' - ' . $mysqli->error;
    }

    $rows = $result->fetch_all();
    $count = count($rows);

    //Login details found
    if($count > 0){

        $row = $rows[0];
        $pwHashFromDB = $row[6];

        if (password_verify($password, $pwHashFromDB)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            echo 'Login Successful!';

            // "SELECT S.firstName, S.lastName, S.email, S.sID, A.adminType, E.uID, S.password ".
            //Store student details in session variables, available until logout
            $_SESSION['firstName'] = $row[0];
            $_SESSION['lastName'] = $row[1];
            $_SESSION['email'] = $row[2];
            $_SESSION['sID'] = $row[3];
            $_SESSION['adminType'] = $row[4];
            $_SESSION['university'] = $row[5];
            $_SESSION['loggedIn'] = TRUE;
            $_SESSION['view'] = 1;
			
			$stmt->close();
			// 1 = SA, 2 = Admin, else = Student
			if($row[4] == 1)
				header('Location: superAdministratorHomepage.php');
			else if($row[4] == 2)
				header('Location: administratorHomepage.php');
			else
				header('Location: studentHomepage.php');			
			
        }
		else {
			// redirect to invalid login page.
        }
	}
}
?>
<html>
	<head>
	
		<title>University Event Login</title>
		<link rel = "stylesheet" href="css/login.css" />
		<link rel = "stylesheet" href="css/main.css" />
	</head>
	<body class = "loginRegister">

	<!-- Login form -->
		<div id= "create">
		
			<h2>Login</h2>
			
			<form name = "create_form" method="get" id="create_form">
			
				<!-- Username -->
				<input class="form-control" type="email" name="email" placeholder="Enter Email" required></br>
				
				<!-- Password -->
				<input class="form-control" type="password" name="password" placeholder="Enter Password" required></br>
				
				<!-- submit form -->
				<button type="submit" >Login</button>
				
				<!-- register redirect -->
				<!-- <input type="button" onclick="window.location = 'register1.php';" value="Register"></br> -->
				<!-- Display Register button -->
				<button class="loginbtn" onclick="document.getElementById('id01').style.display='block'">Register</button><br />
				
			</form>	
		</div>
	</body>
	<!-- Register Popup Window -->
<div id="id01" class="modal">
  <span onclick="document.getElementById('id01').style.display='none'"
        class="close" title="Close Modal">&times;</span>

    <!-- Modal Content -->
    <form class="modal-content-2col animate" action="register.php" method="get">

        <div class="container">
            <div class="leftColumn">
            <label><b>First Name</b></label><br />
            <input type="text" placeholder="Enter First Name" name="firstName" required><br />

            <label><b>Last Name</b></label><br />
            <input type="text" placeholder="Enter Last Name" name="lastName" required><br />

            <label><b>Phone Number</b></label><br />
            <input type="text" placeholder="Enter Phone Number" name="phoneNumber" required><br />

            <!-- Drop down menu to select university from database -->
            <?php universitySelector($mysqli); ?>
            
            <label><b>E-mail</b></label><br />
            <input type="email" placeholder="Enter E-mail" name="email" required><br />

            <label><b>Password</b></label><br />
            <input type="password" placeholder="Enter Password" name="password" required><br />
            </div>

			<!--Handles address form, removed atm -->
            <!--<div class="rightColumn"><?php getAddressInfo(); ?></div> -->

            <button type="submit" >Submit</button><br />
        </div>

        <div class="container" style="background-color:#f1f1f1">
            <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
            <span class="psw"></span>
        </div>
    </form>
</div>
</html>