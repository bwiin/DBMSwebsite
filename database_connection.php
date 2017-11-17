<!-- Standard header for all pages -->
<?php require ('header.php'); ?>

<!-- Standard side menu for all pages -->
<?php require ('sideMenu.php'); ?>

<div id="section">
    <h1>Test MySQL Connection</h1>

    <?php
    include_once 'dbConnect.php';
    include_once 'functions.php';

    //This database_connection page is only temporary. Ultimately
    //a connection class will be created with all the functions needed for this site.

    //Welcome message if user is logged in
    if (  (isset($_SESSION['loggedIn'])) && ($_SESSION['loggedIn'] == TRUE) ) :?>
        <p>Welcome <?php echo htmlentities($_SESSION['email']); ?>!</p><?php endif;

    //Display MySQL settings that are used during connection attempt.
    echo "MySQL Database Connection Settings<br />";
    echo 'HOST: '.HOST."<br />";
    echo 'PORT: '.PORT."<br />";
    echo 'DATABASE: '.DATABASE."<br />";
    echo 'USER: '.USER."<br />";
    echo 'PASSWORD: '.PASSWORD."<br /><br />";

    //Display connection status message from database.
    echo "Connection Status: ".$mysqli->host_info."<br /><br />";

    //Insert SQL Statement
    echo 'Attempting to insert new student.<br />';
    $insert = "INSERT INTO students (firstName, lastName, phone, email, password) VALUES('insert', 'test', 1234567890, 'abc@def.com', 1111)";

    //Check if insert was successful and display result
    if(mysqli_query($mysqli, $insert) === TRUE){ echo "Student inserted into table successfully!<br /><br />"; }
    else{ echo "The following error occurred during insertion: ".$mysqli->error; }

    //Print the entire students table
    printTable($mysqli, 'students');

    //Close database connection
    $mysqli->close();
    ?>

</div>

<!-- Student details for all pages -->
<?php require ('studentDetails.php'); ?>

<!-- Standard footer for all pages -->
<?php require ('footer.php'); ?>
