<div id="section">
    <h1>Register</h1>

    <?php
	session_start();
	include_once 'login.php';
    include_once 'dbConnect.php';

    function cleanPhone($string) {

        $string = str_replace(' ', '', $string);
        return preg_replace('/[^0-9]/', '', $string); // Removes everything other than digits.
    }

    //Create local variables
    //Note: I intend to use $_POST to pass form data, so it doesn't show up in the URL.
    //For some reason, I can't get $_POST to work, still troubleshooting.
    //Passwords will be stored in the database as sha1 hashes. Using plain text for troubleshooting.
    $firstName = $_GET['firstName'];
    $lastName = $_GET['lastName'];
    $phoneNumber = cleanPhone($_GET['phoneNumber']);
    $universityID = $_GET['universityID'];
    $email = $_GET['email'];
    $password = $_GET['password'];
    /*$street1 = $_GET['street1'];
    $street2 = $_GET['street2'];
    $city = $_GET['city'];
    $state = $_GET['state'];
    $zip = $_GET['zip'];
    $country = $_GET['country'];*/
    
    //Display the data from the registration form for testing.
    //The registration form in sideMenu.php checks for empty fields and *@*.* for emails
    /*echo "First Name: $firstName <br />";
    echo "Last Name: $lastName <br />";
    echo "Phone Number: $phoneNumber <br />";
    echo "University: $universityID <br />";
    echo "E-mail: $email <br />";
    echo "Password: $password <br />";
    echo "Street Address: $street1 <br />";
    echo "Apt/Suite/Etc: $street2 <br />";
    echo "City: $city <br />";
    echo "State: $state <br />";
    echo "Zip Code: $zip <br />";
    echo "Country: $country <br />";*/

    //Insert new student into database
    $insert = $mysqli->prepare("INSERT INTO Students (firstName, lastName, phone, email, password) ".
                                "VALUES (?, ?, ?, ?, ?)");

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insert->bind_param("sssss", $firstName, $lastName, $phoneNumber, $email, $hash);

    $insertResult = $insert->execute();
    $new_sID = $mysqli->insert_id;
    $insert->close();

    //Check if insert was successful and display result
    if($insertResult === TRUE){
        echo "Student inserted into table successfully!<br />";

        //Insert student ID into Enrollments table
        $insert = "INSERT INTO enrollments (uID, sID) VALUES(".$universityID.", ".$new_sID.")";
        if(mysqli_query($mysqli, $insert) === TRUE) { echo "Student ID inserted into enrollments successfully!<br />"; }
        else{ echo "<br />The following error occurred during insertion: " . $mysqli->error; }

        //Insert new address into database
        /*$insert = $mysqli->prepare("INSERT INTO Addresses (street1, street2, city, state, zip, country) ".
                                    "VALUES (?, ?, ?, ?, ?, ?)");
        $insert->bind_param("ssssss", $street1, $street2, $city, $state, $zip, $country);
        $insertResult = $insert->execute();
        $new_aID = $mysqli->insert_id;
        $insert->close();
        if($insertResult === TRUE) { echo "Address inserted into table successfully!<br />"; }
        else{ echo "<br />The following error occurred during insertion: " . $mysqli->error; }

        //Insert student ID and address ID into StudentAddresses table
        $insert = "INSERT INTO studentaddresses (aID, sID, addressType) VALUES(".$new_aID.", ".$new_sID.", 'Home')";
        if(mysqli_query($mysqli, $insert) === TRUE) { echo "Student ID and address ID inserted into StudentAddresses successfully!<br />"; }
        else{ echo "<br />The following error occurred during insertion: " . $mysqli->error; }*/

        //Store student details in session variables, available until logout
        $_SESSION['email'] = $email;
        $_SESSION['firstName'] = $firstName;
        $_SESSION['lastName'] = $lastName;
        $_SESSION['sID'] = $new_sID;
        $_SESSION['university'] = $universityID;
        unset($_SESSION['adminType']);
        $_SESSION['loggedIn'] = TRUE;

        //Delete form variables after database insertion
        unset($_GET['firstName']);
        unset($_GET['lastName']);
        unset($_GET['phoneNumber']);
        unset($_GET['universityID']);
        unset($_GET['email']);
        unset($_GET['password']);
        /*unset($_GET['street1']);
        unset($_GET['street2']);
        unset($_GET['city']);
        unset($_GET['state']);
        unset($_GET['zip']);
        unset($_GET['country']);*/

        //Return to home page
        header('Location: login.php');
    }
    else{
        echo "<br />The following error occurred during insertion: ".$mysqli->error;
    }

    //Close database connection
    $mysqli->close();
    ?>

</div>